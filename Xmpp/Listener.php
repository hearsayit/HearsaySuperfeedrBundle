<?php

/*
 * Copyright 2011 Hearsay.
 */

namespace Hearsay\SuperfeedrBundle\Xmpp;

use Symfony\Component\HttpKernel\Log\LoggerInterface;

/**
 * Concrete listener implementation using a Superfeedr connection.  Heavily
 * inspired by the Superfeedr example implementation.
 * @link http://github.com/superfeedr/supeefeedr-php
 * @author Kevin Montag <kevin@hearsay.it>
 */
class Listener implements ListenerInterface
{

    /**
     * @var Superfeedr
     */
    protected $xmpp = null;
    /**
     * @var LoggerInterface
     */
    protected $logger = null;
    /**
     * Timeout in seconds.
     * @var integer
     */
    protected $timeout = -1;

    public function __construct(Superfeedr $xmpp, LoggerInterface $logger)
    {
        $this->xmpp = $xmpp;
        $this->logger = $logger;
    }

    /**
     * Set the timeout after which an exception will be thrown if no messages
     * have been received.
     * @param integer $timeout Timeout in seconds.
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
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
        while (!$this->isDisconnected()) {
            $results = $this->processUntil('message', $this->timeout);
            if (\count($results) === 0) {
                throw new Exception\TimeoutException("Haven't received a message for " . $this->timeout . " seconds.  The connection may have been lost.");
            }
        }
    }
    
    /**
     * Handle a server-initiated message by passing it to our handlers.
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
    

}
