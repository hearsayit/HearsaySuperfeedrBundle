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
     * @var ConnectionFactory
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
     * @param ConnectionFactory $factory The factory to use for creating XMPP
     * instances.
     * @param HandlerInterface $handler The handler to invoke when a
     * notification is received.
     * @param LoggerInterface $logger The logger to note errors.
     */
    public function __construct(ConnectionFactory $factory, HandlerInterface $handler, LoggerInterface $logger)
    {
        $this->factory = $factory;
        $this->handler = $handler;
        $this->logger = $logger;
    }

    /**
     * Receive and handle notifications indefinitely.  This method will not 
     * terminate until the JAXL instance becomes disconnected.
     */
    public function listen()
    {
        $xmpp = $this->factory->createConnection();
        $xmpp->connect();
        $xmpp->processUntil('session_start');
        $xmpp->addXPathHandler('{jabber:client}message/{http://jabber.org/protocol/pubsub#event}event/{http://superfeedr.com/xmpp-pubsub-ext}status', 'handleMessage', $this);
        $xmpp->processUntil('end_stream');
        $xmpp->disconnect();
    }

    public function handleMessage(\XMPPHP_XMLObj $message)
    {
        try {
            $parsed = simplexml_load_string($message->toString());
            $payload = $parsed->event->asXml();
        } catch (\Exception $exception) {
            $this->logger->err("Problem parsing XML: " . $message->toString());
            throw $exception;
        }
        try {
            $this->handler->handleNotification($payload);
        } catch (\Exception $exception) {
            // Log exceptions, but don't stop them from propagating
            $this->logger->err("Caught " . \get_class($exception) . " while handling notification: " . $exception->getMessage());
            throw $exception;
        }
    }

}
