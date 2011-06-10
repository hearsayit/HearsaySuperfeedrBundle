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
        print " DOING THIS \n\n\n\n\n\n";
        print_r($event);
        $this->dispatcher->dispatch(Events::NOTIFICATION_RECEIVED, $event);
        print " DONE IT \n\n\n\n\n\n";
    }

}
