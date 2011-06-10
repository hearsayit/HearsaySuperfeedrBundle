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
 * Factory for generating XMPPHP connections initialized with the user's login
 * info.
 * @author Kevin Montag <kevin@hearsay.it>
 */
class ConnectionFactory
{

    /**
     * XMPP username.
     * @var string
     */
    protected $username = null;
    /**
     * XMPP password.
     * @var string
     */
    protected $password = null;
    /**
     * The port to connect on.
     * @var integer
     */
    protected $port = null;
    /**
     * XMPP host.
     * @var string
     */
    protected $host = null;
    /**
     * XMPP server (defaults to the host).
     * @var string
     */
    protected $server = null;

    /**
     * Standard constructor.
     * @param string $username
     * @param string $password
     * @param integer $port
     * @param string $host
     * @param string $server 
     */
    public function __construct($username, $password, $port = 5222, $host = 'superfeedr.com', $server = null)
    {
        $this->username = $username;
        $this->password = $password;
        $this->port = $port;
        $this->host = $host;
        $this->server = $server;
    }

    /**
     * Get the resource to connect to.
     * @return string The resource.
     */
    protected function getResource()
    {
        return 'superfeedr';
    }
    
    /**
     * Get a connection initialized with the appropriate parameters.
     * @return Xmpp The connection.
     */
    public function createConnection()
    {
        $xmpp = new Xmpp($this->host, $this->port, $this->username, $this->password, $this->getResource(), $this->server, true, 3);
        return $xmpp;
    }

}
