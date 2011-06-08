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

use Hearsay\SuperfeedrBundle\Handler\HandlerInterface;
use Symfony\Component\HttpKernel\Log\LoggerInterface;

/**
 * Receiver class to dispatch notifications to a handler.
 * @author Kevin Montag <kevin@hearsay.it>
 */
class Receiver
{
    /**
     * @var JaxlFactory
     */
    protected $factory = null;
    /**
     * @var HandlerInterface
     */
    protected $handler = null;
    /**
     * @var LoggerInterface
     */
    protected $logger = null;

    /**
     * Standard constructor.
     * @param JaxlFactory $factory The factory to use for creating JAXL
     * instances.
     * @param HandlerInterface $handler The handler to invoke when a
     * notification is received.
     * @param LoggerInterface $logger The logger to note errors.
     */
    public function __construct(JaxlFactory $factory, HandlerInterface $handler, LoggerInterface $logger)
    {
        $this->factory = $factory;
        $this->handler = $handler;
        $this->logger = $logger;
    }

    /**
     * Receive and handle notifications indefinitely.  This method will not 
     * terminate until the JAXL instance becomes disconnected.
     */
    public function start()
    {
        $jaxl = $this->factory->createInstance();
        $jaxl->addPlugin('jaxl_post_handler', array($this, 'handlePost'));
        $jaxl->start();
    }

    /**
     * Callback to handle the raw XML of a notification from the server (message
     * or otherwise).
     * @param string $payload The raw notification XML.
     * @param \JAXL $jaxl The JAXL object on which the message was received.
     */
    public function handlePost($payload, \JAXL $jaxl)
    {        
        $xml = null;
        try {
            $xml = simplexml_load_string($payload);
        } catch (\Exception $exception) {
            // Malformed XML; we're probably not concerned with it
            $xml = null;
            
            // But log an error if something did go wrong
            $messageString = '<message';
            if (\strpos($payload, $messageString) !== false) {
                $this->logger->err("Could not process message: $payload");
            }
        }
        
        if ($xml !== null && $xml->getName() === 'message') {
            $event = $xml->event;
            $this->handler->handleNotification($event->asXml());            
        }        
    }
}
