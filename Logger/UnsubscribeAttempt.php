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
 * Simple structure describing an attempt to unsubscribe from a resource.
 * @author Kevin Montag <kevin@hearsay.it>
 */
class UnsubscribeAttempt
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
     * Standard constructor.
     * @var boolean $successful
     * @var array $urls
     */
    public function __construct($successful, array $urls)
    {
        $this->successful = $successful;
        $this->urls = $urls;
    }

}
