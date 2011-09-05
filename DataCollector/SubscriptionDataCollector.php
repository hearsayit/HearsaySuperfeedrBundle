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

namespace Hearsay\SuperfeedrBundle\DataCollector;

use Hearsay\SuperfeedrBundle\Logger\SubscriptionLogger;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

/**
 * Profiler data collector for Superfeedr subscription requests.
 * @author Kevin Montag <kevin@hearsay.it>
 */
class SubscriptionDataCollector extends DataCollector
{
    /**
     * @var SubscriptionLogger
     */
    protected $logger = null;
    
    /**
     * Standard constructor.
     * @param SubscriptionLogger $logger
     */
    public function __construct(SubscriptionLogger $logger)
    {
        $this->logger = $logger;
    }
    
    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $this->data['subscribeAttempts'] = $this->logger->getSubscribeAttempts();
        $this->data['subscribeAttemptCount'] = $this->logger->countSubscribeAttempts();
        
        $this->data['unsubscribeAttempts'] = $this->logger->getUnsubscribeAttempts();
        $this->data['unsubscribeAttemptCount'] = $this->logger->countUnsubscribeAttempts();
    }
    
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'superfeedr';
    }

    /**
     * Get descriptions of the subscribe attempts made during this request.
     * @return Hearsay\SuperfeedrBundle\Logger\SubscribeAttempt[] The attempts.
     */
    public function getSubscribeAttempts()
    {
        return $this->data['subscribeAttempts'];
    }
    
    /**
     * Get the number of subscribe attempts made during this request.
     * @return integer The attempt count.
     */
    public function getSubscribeAttemptCount()
    {
        return $this->data['subscribeAttemptCount'];
    }
    
    /**
     * Get descriptions of the unsubscribe attempts made during this request.
     * @return Hearsay\SuperfeedrBundle\Logger\UnsubscribeAttempt[] The 
     * attempts.
     */
    public function getUnsubscribeAttempts()
    {
        return $this->data['unsubscribeAttempts'];
    }
    
    /**
     * Get the number of unsubscribe attempts made during this request.
     * @return integer The attempt count.
     */
    public function getUnsubscribeAttemptCount()
    {
        return $this->data['unsubscribeAttemptCount'];
    }
    
}
