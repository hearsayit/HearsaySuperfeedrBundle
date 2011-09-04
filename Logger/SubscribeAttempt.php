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

/**
 * Simple structure for storing data about a subscription attempt.
 * @author Kevin Montag <kevin@hearsay.it>
 */
class SubscribeAttempt
{

    /**
     * Whether the attempt was successful.
     * @var boolean
     */
    public $successful;
    /**
     * The target resource URLs.
     * @var array
     */
    public $urls;
    /**
     * Whether the request included digest updates.
     * @var boolean
     */
    public $digest;

    /**
     * Standard constructor.
     * @param boolean $successful
     * @param array $urls
     * @param boolean $digest 
     */
    public function __construct($successful, array $urls, $digest)
    {
        $this->successful = $successful;
        $this->urls = $urls;
        $this->digest = $digest;
    }

}
