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
        parent::__construct($host, $port, $username, $password, $resource, $server, true, 3);
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
     * {@inheritdoc}
     * @param bool $start Whether to wait for the session to start before 
     * returning.
     */
    public function connect($timeout = 30, $persistent = true, $sendinit = true, $start = true)
    {
        $return = parent::connect($timeout, $persistent, $sendinit);
        $this->connected = true;
        if ($start) {
            $this->processUntil(array('session_start', 'end_stream'));
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
     */
    public function listen()
    {
        if (!($this->isConnected())) {
            $this->connect();
        }

        $this->addXPathHandler('{jabber:client}message/{http://jabber.org/protocol/pubsub#event}event/{http://superfeedr.com/xmpp-pubsub-ext}status', 'handleMessage');
        $this->processUntil('end_stream');
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
