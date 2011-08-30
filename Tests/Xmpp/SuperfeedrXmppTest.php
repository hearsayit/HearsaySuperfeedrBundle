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

namespace Hearsay\SuperfeedrBundle\Tests\Xmpp;

use Hearsay\SuperfeedrBundle\Xmpp\SuperfeedrXmpp;

/**
 * Unit tests for the custom XMPP class.
 * @author Kevin Montag <kevin@hearsay.it>
 */
class SuperfeedrXmppTest extends \PHPUnit_Framework_TestCase
{
    public function testSessionStartRegistered()
    {
        $xmpp = new SuperfeedrXmpp('superfeedr.com', 5222, 'user', 'pass');
        
        $this->assertFalse($xmpp->isSessionStarted(), 'Session marked as started prior to start event.');

        $xmpp->event('session_start');
        $this->assertTrue($xmpp->isSessionStarted(), 'Session not marked as started after start event.');
    }
}
