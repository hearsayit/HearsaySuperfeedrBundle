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

use Hearsay\SuperfeedrBundle\Exception\CouldNotConnectException;

/**
 * JAXL subclass with a custom runner method which operates more cleanly than 
 * the built-in <code>startCore</code>
 * @author Kevin Montag <kevin@hearsay.it>
 */
class Jaxl extends \JAXL
{
    /**
     * Connect and authenticate this instance in stream mode, and start 
     * listening for messages until told to shut down.
     * @throws CouldNotConnectException If we can't reach the server.
     */
    public function start() 
    {
        $this->addPlugin('jaxl_post_connect', array($this, 'startStream'));

        if ($this->connect()) {
            while ($this->stream) {
                $this->getXML();
            }
        } else {
            throw new CouldNotConnectException("Couldn't connect to host: " . $this->host);
        }
    }
}
