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

namespace Hearsay\SuperfeedrBundle\Subscription;

/**
 * Concrete direct subscriber service.
 * @author Kevin Montag <kevin@hearsay.it>
 */
class SubscriptionAdapter implements SubscriptionAdapterInterface
{

    /**
     * Internal tracker for whether subscription requests were successful.
     * @var bool
     */
    private $successful = false;
    /**
     * @var string
     */
    protected $recipient = 'firehoser.superfeedr.com';
    /**
     * @var string
     */
    protected $pubSubXmlns = 'http://jabber.org/protocol/pubsub';
    /**
     * @var string
     */
    protected $superfeedrXmlns = 'http://superfeedr.com/xmpp-pubsub-ext';
    /**
     * The connection to use for subscribing.
     * @var Superfeedr
     */
    protected $xmpp = null;
    /**
     * Subscribe/unsubscribe timeout in seconds.
     * @var integer
     */
    protected $timeout = 5;

    /**
     * Standard constructor.
     * @param Superfeedr $xmpp The connection to use for subscribing.
     */
    public function __construct(Superfeedr $xmpp)
    {
        $this->xmpp = $xmpp;
    }

    /**
     * {@inheritdoc}
     */
    public function subscribeTo(array $urls, $digest)
    {
        return $this->subscribeOrUnsubscribe('subscribe', $urls, $digest);
    }

    /**
     * {@inheritdoc}
     */
    public function unsubscribeFrom(array $urls)
    {
        return $this->subscribeOrUnsubscribe('unsubscribe', $urls, false);
    }

    /**
     * Check whether the most recent subscription or unsubscription was
     * successful.
     * @return bool Subscription success status.
     */
    private function isSuccessful()
    {
        return $this->successful;
    }

    /**
     * Helper function to avoid repetition for subscription and unsubscription.
     * @param string $subscribeNode The name of the element describing a
     * subscribe or unsubscribe request, e.g. 'subscribe' or 'unsubscribe'.
     * @param array $urls The URLs to subscribe/unsubscribe.
     * @param bool $digest Whether to set the Superfeedr 'digest' attribute to
     * true on the subscription tags.
     */
    private function subscribeOrUnsubscribe($subscribeNode, array $urls, $digest)
    {
        $jid = $this->xmpp->user . '@' . $this->xmpp->server;
        $id = $this->xmpp->getId();

        // Build up the IQ request
        $dom = new \DOMDocument();

        $iq = $dom->createElement('iq');
        $iq->setAttribute('type', 'set');
        $iq->setAttribute('to', $this->recipient);
        $iq->setAttribute('from', $jid);
        $iq->setAttribute('id', $id);

        $iq = $dom->appendChild($iq);

        // Create the top-level payload tag
        $pubsub = $dom->createElement('pubsub');
        $pubsub->setAttribute('xmlns', $this->pubSubXmlns);
        $pubsub->setAttribute('xmlns:superfeedr', $this->superfeedrXmlns);
        $pubsub = $iq->appendChild($pubsub);

        // Add subscription requests
        foreach ($urls as $url) {
            $subscribe = $dom->createElement($subscribeNode);
            $subscribe->setAttribute('node', $url);
            $subscribe->setAttribute('jid', $jid);
            if ($digest) {
                $subscribe->setAttribute('superfeedr:digest', 'true');
            }

            $pubsub->appendChild($subscribe);
        }

        $xml = $dom->saveXML($iq);

        // Send and wait for a response
        $this->xmpp->addIdHandler($id, 'handleResponse', $this);

        $this->xmpp->send($xml);
        $this->xmpp->processUntil('handle_subscription', $this->timeout);

        return $this->isSuccessful();
    }

    /**
     * Handle a response to a subscription request from our connection, and set
     * our success state appropriately.
     * @param \XMPPHP_XMLObj $response The server's response to a subscription
     * request.
     */
    public function handleResponse(\XMPPHP_XMLObj $response)
    {
        if ($response->attrs['type'] == 'result') {
            $this->successful = true;
        } else {
            $this->successful = false;
        }
        $this->xmpp->event('handle_subscription');
    }

}
