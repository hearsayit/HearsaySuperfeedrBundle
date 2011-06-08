<?php

/*
 * Copyright (c) 2011 Hearsay News Products, Inc.
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy 
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace Hearsay\SuperfeedrBundle\Xmpp;

/**
 * Factory for creating JAXL instances.
 * @author Kevin Montag
 */
class JaxlFactory
{

    /**
     * @var string
     */
    protected $username = null;
    /**
     * @var string
     */
    protected $password = null;
    /**
     * @var string
     */
    protected $domain = null;
    /**
     * @var string
     */
    protected $host = null;
    /**
     * @var integer
     */
    protected $packetSize = null;
    /**
     * @var string
     */
    protected $pidPath = null;
    /**
     * @var string
     */
    protected $logPath = null;
    /**
     * @var string
     */
    protected $logLevel = null;

    /**
     * Standard constructor.
     * @param string $username The JID to connect with.
     * @param string $password The password to connect with.
     * @param string $domain The XMPP domain to use for login.
     * @param string $host The XMPP host to connect to.
     */
    public function __construct($username, $password, $domain = 'superfeedr.com', $host = 'xmpp.superfeedr.com', $packetSize = 16777216, $pidPath = '/var/run/jaxl.pid', $logPath = '/var/log/jaxl.log', $logLevel = 1)
    {
        $this->username = $username;
        $this->password = $password;
        $this->domain = $domain;
        $this->host = $host;
        $this->packetSize = $packetSize;
        $this->pidPath = $pidPath;
        $this->logPath = $logPath;
        $this->logLevel = $logLevel;
    }

    /**
     * Get a JAXL connection instance initialized with our connection
     * parameters.
     * @return Jaxl The instance.
     */
    public function createInstance()
    {
        $jaxl = new Jaxl(array(
            'user' => $this->username,
            'pass' => $this->password,
            'domain' => $this->domain,
            'host' => $this->host,
            'pidPath' => $this->pidPath,
            'logPath' => $this->logPath,
            'logLevel' => $this->logLevel,
            'getPktSize' => $this->packetSize,
            'authType' => 'PLAIN',
            'mode' => 'cli',
        ));
        $jaxl->requires('JAXL0060');
        
        return $jaxl;
    }

}
