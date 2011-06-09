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

namespace Hearsay\SuperfeedrBundle\Test;

use Hearsay\SuperfeedrBundle\Xmpp\SubscriberInterface;

/**
 * Dummy subscriber implementation which just stores a list of the feeds it has
 * subscribed to and unsubscribed from.  Intended for use in testing.
 * @author Kevin Montag <kevin@hearsay.it>
 */
class Subscriber implements SubscriberInterface
{
    /**
     * @var array
     */
    private $subscribed = array();
    /**
     * @var array
     */
    private $unsubscribed = array();

    /**
     * {@inheritdoc}
     */
    public function subscribe($urls, $digest)
    {
        if (!(is_array($urls))) {
            $urls = array($urls);
        }
        
        foreach ($urls as $url) {
            $this->subscribed[$url] = $digest;
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function unsubscribe($urls)
    {
        if (!(is_array($urls))) {
            $urls = array($urls);
        }
        
        foreach ($urls as $url) {
            $this->unsubscribed[$url] = true;
        }        
    }

    /**
     * Get the map of subscribed feeds, as an array of [url] => [digest] pairs.
     * @return array The subscribed feeds since construction.
     */
    public function getSubscribed()
    {
        return $this->subscribed;
    }

    /**
     * Check whether we've subscribed to a particular feed.
     * @param string $url The feed URL.
     * @return bool Whether we've subscribed to the feed.
     */
    public function isSubscribed($url)
    {
        return isset($this->subscribed[$url]);
    }

    /**
     * Get the map of unsubscribed feeds, as an array of [url] => [true] pairs.
     * @return array The unsubscribed feeds since construction.
     */
    public function getUnsubscribed()
    {
        return $this->unsubscribed;
    }
    
    /**
     * Check whether we've unsubscribed from a particular feed.
     * @param string $url The feed URL.
     * @return bool Whether we've unsubscribed from the feed.
     */
    public function isUnsubscribed($url)
    {
        return isset($this->unsubscribed[$url]);
    }

}
