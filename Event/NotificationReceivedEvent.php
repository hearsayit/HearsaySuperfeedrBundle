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
    protected $url = null;
    /**
     * @var array
     */
    protected $items = null;
    /**
     * @var bool
     */
    protected $digest = null;

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
