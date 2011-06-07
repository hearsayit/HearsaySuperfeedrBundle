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

namespace Hearsay\SuperfeedrBundle\Subscription;

use Hearsay\SuperfeedrBundle\Xmpp\JaxlFactory;

/**
 * Service to subscribe or unsubscribe from notifications on feeds.
 * @author Kevin Montag <kevin@hearsay.it>
 */
class Subscriber
{

    /**
     * @var JaxlFactory
     */
    protected $jaxlFactory = null;

    /**
     * @var string
     */
    protected $recipient = null;
    
    public function __construct(JaxlFactory $jaxlFactory, $recipient = 'firehoser.superfeedr.com', $defaultDigest = false)
    {
        $this->jaxlFactory = $jaxlFactory;
        $this->recipient = $recipient;
    }

    protected function submitRequest($payload)
    {
        $jaxl = $this->jaxlFactory->createInstance();
        
        $recipient = $this->recipient;

        \JAXL0060::init($jaxl);
        \JAXL0060::subscribe($jaxl, $recipient, 'kmontag@superfeedr.com', 'http://superfeedr.com/dummy.xml');
        
        /*
        $jaxl->addPlugin('jaxl_post_auth', function($unused, $jaxl) use ($payload, $recipient) {
            $jaxl->sendIQ('set', $payload, $recipient, 'kmontag@superfeedr.com', function ($response, $jaxl) {
                print_r($response);
            });
        });
        
        $jaxl->startCore('stream');
         * 
         */
    }

    /**
     * Subscribe to receive updates from the given resource.
     * @param string $url The URL of the resource.
     * @param bool|null $digest Whether to subscribe for digest updates on the
     * resources, or null to use the digest default provided at construction.
     */
    public function subscribe($url) {
        $payload = <<<PAY
 <pubsub xmlns="http://jabber.org/protocol/pubsub" xmlns:superfeedr="http://superfeedr.com/xmpp-pubsub-ext">
  <subscribe node="http://superfeedr.com/dummy.xml" jid="kmontag@superfeedr.com"/>    
 </pubsub>
PAY;
        $this->submitRequest($payload);
    }

}
