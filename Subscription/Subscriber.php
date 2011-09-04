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

use Hearsay\SuperfeedrBundle\Logger\SubscriptionLogger;

/**
 * Resource subscriber.  Handles actual subscription as well as logging.
 * @author Kevin Montag <kevin@hearsay.it>
 */
class Subscriber implements SubscriberInterface
{

    /**
     * @var SubscriptionAdapterInterface
     */
    protected $subscriptionAdapter = null;
    /**
     * @var SubscriptionLogger
     */
    protected $logger = null;

    /**
     * Standard constructor.
     * @param SubscriptionAdapterInteface $subscriptionAdapter Direct
     * subscription adapter.
     * @param SubscriptionLogger $logger Logger.
     */
    public function __construct(SubscriptionAdapterInterface $subscriptionAdapter, SubscriptionLogger $logger)
    {
        $this->subscriptionAdapter = $subscriptionAdapter;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function subscribeTo($urls, $digest)
    {
        // Convert to array
        if (!is_array($urls)) {
            $urls = array($urls);
        }

        // Perform the subscriptions
        $successful = $this->subscriptionAdapter->subscribeTo($urls, $digest);
        
        // Log the attempt
        $this->logger->logSubscribeAttempt($successful, $urls, $digest);
    }
    
    /**
     * {@inheritdoc}
     */
    public function unsubscribeFrom($urls)
    {
        // Convert to array
        if (!is_array($urls)) {
            $urls = array($urls);
        }
        
        // Perform the unsubscriptions
        $successful = $this->subscriptionAdapter->unsubscribeFrom($urls);
        
        // Log the attempt
        $this->logger->logUnsubscribeAttempt($successful, $urls);
    }

}
