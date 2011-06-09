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

use Hearsay\SuperfeedrBundle\Exception;
use Hearsay\SuperfeedrBundle\Xmpp\JaxlFactory;

/**
 * Service to subscribe or unsubscribe from notifications on feeds.
 * @author Kevin Montag <kevin@hearsay.it>
 */
class Subscriber implements SubscriberInterface
{

    /**
     * @var JaxlFactory
     */
    protected $jaxlFactory = null;
    /**
     * @var string
     */
    protected $recipient = null;
    /**
     * @var string
     */
    protected $pubSubXmlns = 'http://jabber.org/protocol/pubsub';
    /**
     * @var string
     */
    protected $superfeedrXmlns = 'http://superfeedr.com/xmpp-pubsub-ext';

    /**
     * Standard constructor.
     * @param JaxlFactory $jaxlFactory Factory for creating JAXL instances.
     * @param string $recipient Value of the 'to' attribute on subscribe and
     * unsubscribe requests.
     */
    public function __construct(JaxlFactory $jaxlFactory, $recipient = 'firehoser.superfeedr.com')
    {
        $this->jaxlFactory = $jaxlFactory;
        $this->recipient = $recipient;
    }

    /**
     * Helper function to avoid repetition for subscription and unsubscription.
     * @param string $subscribeNode The name of the element describing a
     * subscribe or unsubscribe request, e.g. 'subscribe' or 'unsubscribe'.
     * @param string|array $urls The URL or URLs to subscribe/unsubscribe.
     * @param bool $digest Whether to set the Superfeedr 'digest' attribute to
     * true on the subscription tags.
     */
    private function subscribeOrUnsubscribe($subscribeNode, $urls, $digest)
    {
        // Always work with an array of URLs
        if (!(\is_array($urls))) {
            $urls = array($urls);
        }

        // Create a JAXL instance so we can get our JID
        $jaxl = $this->jaxlFactory->createInstance();

        $dom = new \DOMDocument();

        // Create the top-level payload tag
        $pubsub = $dom->createElement('pubsub');
        $pubsub->setAttribute('xmlns', $this->pubSubXmlns);
        $pubsub->setAttribute('xmlns:superfeedr', $this->superfeedrXmlns);
        $pubsub = $dom->appendChild($pubsub);

        // Add subscription requests
        foreach ($urls as $url) {
            $subscribe = $dom->createElement($subscribeNode);
            $subscribe->setAttribute('node', $url);
            $subscribe->setAttribute('jid', $jaxl->bareJid);
            if ($digest) {
                $subscribe->setAttribute('superfeedr:digest', 'true');
            }

            $pubsub->appendChild($subscribe);
        }

        $payload = $dom->saveXML($pubsub);
        $recipient = $this->recipient;
        $success = false;

        $jaxl->addPlugin('jaxl_post_auth', function($unused, $jaxl) use ($payload, $recipient, &$success) {
                    // Send the subscription request
                    $jaxl->sendIQ('set', $payload, $recipient, $jaxl->bareJid, function($response, \JAXL $jaxl) use (&$success) {
                                if ($response['type'] === 'result') {
                                    $success = true;
                                }
                                $jaxl->shutdown();
                            });
                });
        $jaxl->start();
        return $success;
    }

    /**
     * {@inheritdoc}
     */
    public function subscribe($urls, $digest)
    {
        return $this->subscribeOrUnsubscribe('subscribe', $urls, $digest);
    }

    /**
     * {@inheritdoc}
     */
    public function unsubscribe($urls)
    {
        return $this->subscribeOrUnsubscribe('unsubscribe', $urls, false);
    }

}
