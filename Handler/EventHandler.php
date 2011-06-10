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

namespace Hearsay\SuperfeedrBundle\Handler;

use Hearsay\SuperfeedrBundle\Events;
use Hearsay\SuperfeedrBundle\Event\NotificationReceivedEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Service to handle XMPP notifications by dispatching an event.
 * @author Kevin Montag
 */
class EventHandler implements HandlerInterface
{
    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher = null;

    /**
     * Standard constructor.
     * @param EventDispatcherInterface $dispatcher The event dispatcher to use.
     */
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function handleNotification($payload)
    {   
        $xml = simplexml_load_string($payload);
   
        // Get the URL
        $url = (string)($xml->status->attributes()->feed);

        // Get the digest status
        $status = $xml->status;
        $digest = false;
        $digestAttribute = $status->attributes()->digest;
        if ($digestAttribute) {
            $digest = true;
        }
        
        // Get the entries
        $entries = array();
        foreach ($xml->items->item as $item) {
            $entry = $item->entry->asXml();
            $entries[] = $entry;
        }
        $event = new NotificationReceivedEvent($url, $entries, $digest);
        
        // Dispatch the event
        $this->dispatcher->dispatch(Events::NOTIFICATION_RECEIVED, $event);
    }

}
