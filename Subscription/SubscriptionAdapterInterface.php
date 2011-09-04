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

/**
 * Interface for services which can perform direct subscription to Superfeedr
 * resources.  Should generally be used in conjunction with a 
 * <code>SubscriberInterface</code>, which offers more complete subscription
 * functionality.
 * @author Kevin Montag <kevin@hearsay.it>
 */
interface SubscriptionAdapterInterface
{

    /**
     * Subscribe to a set of resources.
     * @param array $urls The resource URLs.
     * @param boolean $digest Whether to subscribe for digest updates.
     * @return boolean Whether the subscription was successful.
     */
    public function subscribeTo(array $urls, $digest);

    /**
     * Unsubscribe from a set of resources.
     * @param array $urls The resource URLs.
     * @return boolean Whether the unsubscription was successful.
     */
    public function unsubscribeFrom(array $urls);
}
