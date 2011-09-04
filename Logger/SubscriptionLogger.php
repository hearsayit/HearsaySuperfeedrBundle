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

namespace Hearsay\SuperfeedrBundle\Logger;

use Symfony\Component\HttpKernel\Log\LoggerInterface;

/**
 * Service for logging subscribe/unsubscribe requests.
 * @author Kevin Montag <kevin@hearsay.it>
 */
class SubscriptionLogger
{

    /**
     * @var LoggerInterface
     */
    protected $logger = null;
    /**
     * Internal tracker for subscription attempts.
     * @var array
     */
    private $subscribeAttempts = array();
    /**
     * Internal tracker for unsubscription attempts.
     * @var array
     */
    private $unsubscribeAttempts = array();

    /**
     * Standard constructor.
     * @param LoggerInterface $logger 
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Log a resource subscription attempt.
     * @param boolean $successful Whether the attempt succeeded.
     * @param string $urls The resource URLs being subscribed to.
     * @param boolean $digest Whether we were attempting to subscribe to digest
     * notifications on the resource.
     */
    public function logSubscribeAttempt($successful, array $urls, $digest)
    {
        // Write the actual logs
        foreach ($urls as $url) {
            if ($successful) {
                $this->logger->info(sprintf('Subscribed to resource "%s" (%sreceiving digest updates)', $url, $digest ? '' : 'NOT '));
            } else {
                $this->logger->warn(sprintf('Unsuccessful subscription attempt to resource "%s" (%sreceiving digest updates)', $url, $digest ? '' : 'NOT '));
            }
        }

        // Store the data directly
        $this->subscribeAttempts[] = new SubscribeAttempt($successful, $urls, $digest);
    }

    /**
     * Log a resource unsubscription attempt.
     * @param boolean $successful Whether the attempt succeeded.
     * @param array $url The resource URLs.
     */
    public function logUnsubscribeAttempt($successful, array $urls)
    {
        // Write the actual logs
        foreach ($urls as $url) {
            if ($successful) {
                $this->logger->info(sprintf('Unsubscribed from resource "%s"', $url));
            } else {
                $this->logger->warn(sprintf('Unsuccessful unsubscription attempt from resource "%s"', $url));
            }
        }
        
        // Store the data directly
        $this->unsubscribeAttempts[] = new UnsubscribeAttempt($successful, $urls);
    }

    /**
     * Get the number of subscription attempts since we started logging.
     * @return integer Attempts count.
     */
    public function countSubscribeAttempts()
    {
        return count($this->subscribeAttempts);
    }

    /**
     * Get information about the subscribe attempts since we started logging.
     * @return SubscribeAttempt[] The attempt information, in the order they
     * were made.
     */
    public function getSubscribeAttempts()
    {
        return $this->subscribeAttempts;
    }

    /**
     * Get the number of unsubscription attempts since we started logging.
     * @return integer Attempts count.
     */
    public function countUnsubscribeAttempts()
    {
        return count($this->unsubscribeAttempts);
    }

    /**
     * Get information about the unsubscribe attempts since we started logging.
     * @return UnsubscribeAttempt[] The attempt information, in the order they
     * were made.
     */
    public function getUnsubscribeAttempts()
    {
        return $this->unsubscribeAttempts;
    }

}
