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

namespace Hearsay\SuperfeedrBundle\Listening;

use Hearsay\SuperfeedrBundle\Exception;
use Hearsay\SuperfeedrBundle\Xmpp\SuperfeedrXmpp;
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
     * @var SuperfeedrXmpp
     */
    protected $xmpp = null;
    /**
     * @var LoggerInterface
     */
    protected $logger = null;
    /**
     * Timeout in seconds (negative for infinite).
     * @var integer
     */
    protected $timeout = -1;
    /**
     * Message handlers.
     * @var NotificationHandlerInterface[]
     */
    protected $handlers = array();

    /**
     * Standard constructor.
     * @param SuperfeedrXmpp $xmpp XMPP connection.
     * @param LoggerInterface $logger Logger.
     */
    public function __construct(SuperfeedrXmpp $xmpp, LoggerInterface $logger)
    {
        $this->xmpp = $xmpp;
        $this->logger = $logger;
    }

    /**
     * Set the timeout after which an exception will be thrown if no messages
     * have been received.
     * @param integer $timeout Timeout in seconds (negative for infinite).
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
        $this->xmpp->connectAndStartSession($this->timeout);

        // Add the message handler
        $this->xmpp->addXPathHandler('{jabber:client}message/{http://jabber.org/protocol/pubsub#event}event/{http://superfeedr.com/xmpp-pubsub-ext}status', 'handleMessage', $this);

        // Process messages repeatedly
        while (!$this->xmpp->isDisconnected()) {
            $results = $this->xmpp->processUntil('message', $this->timeout);
            if (count($results) === 0) {
                $this->logger->warn(sprintf('Superfeedr listener timed out (no messages for %d seconds).', $this->timeout));
                throw new Exception\TimeoutException(sprintf('No messages for %d seconds.  The connection may have been lost.', $this->timeout));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addNotificationHandler(NotificationHandlerInterface $handler)
    {
        $this->handlers[] = $handler;
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
            foreach ($this->handlers as $handler) {
                $handler->handleNotification($payload);
            }
        } catch (\Exception $exception) {
            // Log exceptions, but don't stop them from propagating
            if ($this->logger !== null) {
                $this->logger->err("Caught " . \get_class($exception) . " while handling notification: " . $exception->getMessage());
            }
            throw $exception;
        }
    }

}
