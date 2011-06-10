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
use Hearsay\SuperfeedrBundle\Handler\HandlerInterface;
use Symfony\Component\HttpKernel\Log\LoggerInterface;

/**
 * Custom XMPP instance for dealing with Superfeedr-related actions.  Heavily
 * inspired by the Superfeedr example implementation.
 * @link http://github.com/superfeedr/supeefeedr-php
 * @author Kevin Montag <kevin@hearsay.it>
 */
class Superfeedr extends \XMPPHP_XMPP implements SubscriberInterface, ListenerInterface
{

    /**
     * @var bool
     */
    private $connected = false;
    /**
     * @var HandlerInterface
     */
    protected $handler = null;
    /**
     * @var LoggerInterface
     */
    protected $logger = null;
    /**
     * Timeout to expect messages from the listener.
     * @var integer
     */
    protected $listenerTimeout = -1;

    /**
     * Standard constructor; reorder the options to make more sense for the
     * Superfeedr context.
     * @param string $username Superfeedr username.
     * @param string $password Superfeedr password.
     * @param integer $port Port to connect on.
     * @param string $host Domain name for username.
     * @param string $server Server to connect on.
     */
    public function __construct($username, $password, $port = 5222, $host = 'superfeedr.com', $server = null, $resource = 'superfeedr')
    {
        parent::__construct($host, $port, $username, $password, $resource, $server);
    }

    /**
     * Set the handler to manage messages from the server.
     * @param HandlerInterface $handler The handler.
     */
    public function setHandler(HandlerInterface $handler)
    {
        $this->handler = $handler;
    }

    /**
     * Set the logger for events.
     * @param LoggerInterface $logger The logger.
     */
    public function setLogger(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }

    /**
     * Set the timeout after which an exception will be thrown when listening
     * for messages.
     * @param integer $listenerTimeout Timeout in seconds.
     */
    public function setListenerTimeout($listenerTimeout)
    {
        $this->listenerTimeout = $listenerTimeout;
    }

    /**
     * {@inheritdoc}
     * @param bool $start Whether to wait for the session to start before 
     * returning.
     */
    public function connect($timeout = 30, $persistent = true, $sendinit = true, $start = true)
    {
        $return = parent::connect($timeout, $persistent, $sendinit);
        $this->connected = true;
        if ($start) {
            $this->processUntil(array('session_start', 'end_stream'), $timeout);
        }
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
     * {@inheritdoc}
     */
    protected function bufferComplete($buff)
    {
        // Hack to fix the bug on disconnect
        if ($this->sent_disconnect) {
            if ($buff === $this->stream_end) {
                return true;
            }
        }
        return parent::bufferComplete($buff);
    }

    /**
     * {@inheritdoc}
     * @throws Exception\TimeoutException If no message is received for the
     * specified timeout period.
     */
    public function listen()
    {
        if (!($this->isConnected())) {
            $this->connect();
        }

        $this->addXPathHandler('{jabber:client}message/{http://jabber.org/protocol/pubsub#event}event/{http://superfeedr.com/xmpp-pubsub-ext}status', 'handleMessage');
        while (!$this->isDisconnected()) {
            $results = $this->processUntil('message', $this->listenerTimeout);
            if (\count($results) === 0) {
                throw new Exception\TimeoutException("Haven't received a message for " . $this->listenerTimeout . " seconds.  The connection may have been lost.");
            }
        }
    }

    /**
     * Handle a server-initiated message by passing it to our handler.
     * @param \XMPPHP_XMLObj $message The message.
     */
    public function handleMessage(\XMPPHP_XMLObj $message)
    {
        try {
            $parsed = simplexml_load_string($message->toString());
            $payload = $parsed->event->asXml();
        } catch (\Exception $exception) {
            if ($this->logger !== null) {
                $this->logger->err("Problem parsing XML: " . $message->toString());
            }
            throw $exception;
        }
        try {
            $this->handler->handleNotification($payload);
        } catch (\Exception $exception) {
            // Log exceptions, but don't stop them from propagating
            if ($this->logger !== null) {
                $this->logger->err("Caught " . \get_class($exception) . " while handling notification: " . $exception->getMessage());
            }
            throw $exception;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function subscribeTo($urls, $digest)
    {
        if (!($this->isConnected())) {
            $this->connect();
        }
        $helper = new SubscriptionHelper($this);
        return $helper->doSubscribe($urls, $digest);
    }

    /**
     * {@inheritdoc}
     */
    public function unsubscribeFrom($urls)
    {
        if (!($this->isConnected())) {
            $this->connect();
        }

        $helper = new SubscriptionHelper($this);
        return $helper->doUnsubscribe($urls);
    }

}
