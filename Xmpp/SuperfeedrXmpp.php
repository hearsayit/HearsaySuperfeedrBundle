<?php

/*
 * The Hearsay Superfeedr bundle for Symfony2.
 * Copyright (C) 2011 Hearsay News Products, Inc.
 *
 * This program is free software; you can redistribute it and/or modify it 
 * under the terms of the GNU General Public License as published by the Free 
 * Software Foundation; either version 2 of the License, or (at your option) any
 * later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT 
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more 
 * details.
 *
 * You should have received a copy of the GNU General Public License along with 
 * this program; if not, write to the Free Software Foundation, Inc., 51 
 * Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA. 
 */

namespace Hearsay\SuperfeedrBundle\Xmpp;

use Hearsay\SuperfeedrBundle\Exception;

/**
 * Custom XMPP class tuned specifically for high-throughput Superfeedr
 * interaction.  Processes input more cautiously than the standard
 * XMPPHP implementation, including timing out more aggressively and providing
 * additional consistency checks for high-throughput connections.
 * @author Kevin Montag <kevin@hearsay.it>
 */
class SuperfeedrXmpp extends \XMPPHP_XMPP
{

    /**
     * Running tally of the number of incomplete unchanged buffer checks we've 
     * performed consecutively.
     * @var integer
     */
    private $numIncomplete = 0;
    /**
     * The previously-checked contents of the buffer when checking for buffer
     * completeness.
     * @var string
     */
    private $prevBuffer = null;
    /**
     * Internal tracker for whether we're connected.
     * @var bool
     */
    private $connected = false;
    /**
     * Internal tracker for whether the session is started.
     * @var bool
     */
    protected $sessionStarted = false;
    /**
     * The number of times to check an unchanging incomplete buffer for 
     * completeness before performing a more expensive completeness check.
     * @var integer
     */
    protected $expensiveIterations = 100;
    /**
     * The number of iterations to check an unchanging incomplete buffer for
     * completeness before assuming that the connection has been lost.
     * @var integer
     */
    protected $maxIncompleteIterations = 10000;

    /**
     * {@inheritdoc}
     */
    public function __construct($host, $port, $username, $password, $resource = 'superfeedr', $server = null)
    {
        parent::__construct($host, $port, $username, $password, $resource, $server);

        // TODO: Fix this in XMPPHP implementation, not this subclass
        $this->until = array();
        
        // Catch when a session is started
        $this->addEventHandler('session_start', 'sessionStarted', $this);
    }

    /**
     * Set the number of times to check an unchanging incomplete buffer for
     * completeness before performing a more expensive, but more thorough,
     * completeness check.
     * @param integer $expensiveIterations Number of iterations.
     */
    public function setExpensiveIterations($expensiveIterations)
    {
        $this->expensiveIterations = $expensiveIterations;
    }

    /**
     * Set the number of times we should check an unchanging incomplete buffer
     * for completeness before assuming that the connection has been lost.
     * @param integer $maxIncompleteIterations Number of iterations.
     */
    public function setMaxIncompleteIterations($maxIncompleteIterations)
    {
        $this->maxIncompleteIterations = $maxIncompleteIterations;
    }

    /**
     * Responder to the <code>session_start</code> event.  Store the started
     * state.
     */
    protected function sessionStarted()
    {
        $this->sessionStarted = true;
    }

    /**
     * Check whether the session has been started.
     * @return bool Session started state.
     */
    public function isSessionStarted()
    {
        return $this->sessionStarted;
    }
    
    /**
     * Custom connection function which also stores our connected state.
     * 
     * {@inheritdoc}
     */
    public function connect($timeout = 30, $persistent = false, $sendinit = true)
    {
        $return = parent::connect($timeout, $persistent, $sendinit);
        $this->connected = !($this->isDisconnected());
        return $return;
    }

    /**
     * Check whether we're currently connected.
     * @return bool Whether we're connected.
     */
    public function isConnected()
    {
        return $this->connected;
    }

    /**
     * Convenience method to check our current connection and session-started
     * state, and perform both of these actions if appropriate.  Does not
     * reconnect or restart the session.
     * @param integer $timeout Timeout for both operations.
     * @param boolean $persistent Persistence flag for the connect operation.
     * @param boolean $sendinit Init flag for the connect operation.
     */
    public function connectAndStartSession($timeout = 30, $persistent = false, $sendinit = true)
    {
        // Connect and start session
        if (!($this->isConnected())) {
            // We enforce a positive timeout on the connection
            $this->connect($timeout > 0 ? $timeout : 30, $persistent, $sendinit);
        }
        if (!($this->isSessionStarted())) {
            $this->processUntil(array('session_start', 'end_stream'), $timeout);
        }
    }
    
    /**
     * Custom hacky buffer-complete check to avoid potential infinite loops
     * in the standard XMPPHP library.
     * 
     * {@inheritdoc}
     */
    protected function bufferComplete($buff)
    {
        $complete = parent::bufferComplete($buff);

        // TODO: Submit fixes to XMPPHP
        if (!$complete) {
            // Fix the bug on disconnect
            if ($this->sent_disconnect) {
                if ($buff === $this->stream_end) {
                    $complete = true;
                }
            }
        }

        if (!$complete && $this->numIncomplete > 0 && ($this->numIncomplete % $this->expensiveIterations === 0)) {
            // Check for a complete but stuck buffer
            $complete = $this->isMultipleElements($buff);
        }

        if (!$complete && $this->numIncomplete > $this->maxIncompleteIterations) {
            // Hack to bail out if the buffer is disconnected
            throw new Exception\TimeoutException("Incomplete buffer after " . $this->maxIncompleteIterations . " polling iterations.");
        }

        // Reset or advance our running tally of iterations
        if ($complete || $buff !== $this->prevBuffer) {
            $this->numIncomplete = 0;
        } else {
            $this->numIncomplete++;
        }

        // Store the current buffer so we can compare to the next one received
        $this->prevBuffer = $buff;

        return $complete;
    }

    /**
     * Check to see if the given XML substring contains one or more complete
     * stanzas.  This is essentially a more complete, but poorer-performing,
     * version of <code>bufferComplete</code>, which we run less frequently to 
     * prevent infinite loops.
     * @param string $xml The XML substring to check.
     * @return bool Whether this is a closed XML substring.
     */
    protected function isMultipleElements($xml)
    {
        $xml = \trim($xml);

        // Base case
        if ($xml === '') {
            return true;
        }

        // Otherwise, pull out the first stanza (if any) and recurse
        $matches = array();
        $match = \preg_match('/^<([A-Za-z]+)([\s].*)?>/', $xml, $matches);
        if ($match) {
            $opening = $matches[1];
        } else {
            return false;
        }

        // Look for a matching closing tag
        $closing = '</' . $opening . '>';
        $closingPos = \strpos($xml, $closing);

        if ($closingPos === false) {
            // If not found, we can just return
            return false;
        } else {
            // Otherwise, cut out the first stanza and check the rest of the string
            $nextPos = $closingPos + \strlen($closing);
            return $this->isMultipleElements(\substr($xml, $nextPos));
        }
    }

}
