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

namespace Hearsay\SuperfeedrBundle\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Event fired when a Superfeedr notification is received.
 * @author Kevin Montag <kevin@hearsay.it>
 */
class NotificationReceivedEvent extends Event
{

    /**
     * @var string
     */
    private $url = null;
    /**
     * @var array
     */
    private $items = null;
    /**
     * @var bool
     */
    private $digest = null;

    /**
     * Standard constructor.
     * @param string $url The URL of the resource corresponding to the
     * notification.
     * @param array $items The items received along with this notification.
     * @param bool $digest Whether this is a digest notification.
     */
    public function __construct($url, array $items, $digest)
    {
        $this->url = $url;
        $this->items = $items;
        $this->digest = $digest;
    }

    /**
     * Get the URL of the resource for which this notification was recieved.
     * @return string The URL.
     */
    public function getResourceUrl()
    {
        return $this->url;
    }

    /**
     * Get the raw XML for each of the items received with this notification.
     * @return string[] The raw XML.
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Check whether this is a digest notification (as opposed to a push
     * notification).
     * @return bool Whether this is a digest notification.
     */
    public function isDigest()
    {
        return $this->digest;
    }

}
