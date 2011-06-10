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
    public function subscribeTo($urls, $digest)
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
    public function unsubscribeFrom($urls)
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
