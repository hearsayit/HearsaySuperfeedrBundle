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

namespace Hearsay\SuperfeedrBundle\Tests\Subscription;

use Hearsay\SuperfeedrBundle\Subscription\SubscriptionAdapter;

/**
 * Unit tests for the live subscription adapter.
 * @author Kevin Montag <kevin@hearsay.it>
 */
class SubscriptionAdapterTest extends \PHPUnit_Framework_TestCase
{
    public function testValidSubscription()
    {
        $xmpp = $this->getMockBuilder('Hearsay\SuperfeedrBundle\Xmpp\SuperfeedrXmpp')
                ->setConstructorArgs(array('xmpp.superfeedr.com', 5222, 'kmontag', 'password', 'superfeedr', 'superfeedr.com'))
                ->getMock();

        // Actual sent/received XML
        $sent = json_decode('"<iq type=\"set\" to=\"firehoser.superfeedr.com\" from=\"kmontag@superfeedr.com\" id=\"3\"><pubsub xmlns=\"http:\/\/jabber.org\/protocol\/pubsub\" xmlns:superfeedr=\"http:\/\/superfeedr.com\/xmpp-pubsub-ext\"><subscribe node=\"http:\/\/superfeedr.com\/dummy.xml\" jid=\"kmontag@superfeedr.com\" superfeedr:digest=\"true\"\/><\/pubsub><\/iq>"');
        $received = unserialize(json_decode('"O:13:\"XMPPHP_XMLObj\":5:{s:4:\"name\";s:2:\"iq\";s:2:\"ns\";s:13:\"jabber:client\";s:5:\"attrs\";a:4:{s:4:\"from\";s:24:\"firehoser.superfeedr.com\";s:2:\"to\";s:33:\"kmontag@superfeedr.com\/superfeedr\";s:4:\"type\";s:6:\"result\";s:2:\"id\";s:1:\"3\";}s:4:\"subs\";a:1:{i:0;O:13:\"XMPPHP_XMLObj\":5:{s:4:\"name\";s:6:\"pubsub\";s:2:\"ns\";s:33:\"http:\/\/jabber.org\/protocol\/pubsub\";s:5:\"attrs\";a:1:{s:5:\"xmlns\";s:33:\"http:\/\/jabber.org\/protocol\/pubsub\";}s:4:\"subs\";a:1:{i:0;O:13:\"XMPPHP_XMLObj\":5:{s:4:\"name\";s:12:\"subscription\";s:2:\"ns\";s:33:\"http:\/\/jabber.org\/protocol\/pubsub\";s:5:\"attrs\";a:3:{s:4:\"node\";s:31:\"http:\/\/superfeedr.com\/dummy.xml\";s:3:\"jid\";s:22:\"kmontag@superfeedr.com\";s:12:\"subscription\";s:10:\"subscribed\";}s:4:\"subs\";a:1:{i:0;O:13:\"XMPPHP_XMLObj\":5:{s:4:\"name\";s:6:\"status\";s:2:\"ns\";s:37:\"http:\/\/superfeedr.com\/xmpp-pubsub-ext\";s:5:\"attrs\";a:1:{s:5:\"xmlns\";s:37:\"http:\/\/superfeedr.com\/xmpp-pubsub-ext\";}s:4:\"subs\";a:7:{i:0;O:13:\"XMPPHP_XMLObj\":5:{s:4:\"name\";s:4:\"http\";s:2:\"ns\";s:37:\"http:\/\/superfeedr.com\/xmpp-pubsub-ext\";s:5:\"attrs\";a:1:{s:4:\"code\";s:3:\"304\";}s:4:\"subs\";a:0:{}s:4:\"data\";s:20:\"Content not modified\";}i:1;O:13:\"XMPPHP_XMLObj\":5:{s:4:\"name\";s:10:\"next_fetch\";s:2:\"ns\";s:37:\"http:\/\/superfeedr.com\/xmpp-pubsub-ext\";s:5:\"attrs\";a:0:{}s:4:\"subs\";a:0:{}s:4:\"data\";s:25:\"2011-09-05T00:50:08+00:00\";}i:2;O:13:\"XMPPHP_XMLObj\":5:{s:4:\"name\";s:5:\"title\";s:2:\"ns\";s:37:\"http:\/\/superfeedr.com\/xmpp-pubsub-ext\";s:5:\"attrs\";a:0:{}s:4:\"subs\";a:0:{}s:4:\"data\";s:19:\"The Dummy Time Feed\";}i:3;O:13:\"XMPPHP_XMLObj\":5:{s:4:\"name\";s:6:\"period\";s:2:\"ns\";s:37:\"http:\/\/superfeedr.com\/xmpp-pubsub-ext\";s:5:\"attrs\";a:0:{}s:4:\"subs\";a:0:{}s:4:\"data\";s:3:\"900\";}i:4;O:13:\"XMPPHP_XMLObj\":5:{s:4:\"name\";s:10:\"last_fetch\";s:2:\"ns\";s:37:\"http:\/\/superfeedr.com\/xmpp-pubsub-ext\";s:5:\"attrs\";a:0:{}s:4:\"subs\";a:0:{}s:4:\"data\";s:25:\"2011-09-05T00:35:48+00:00\";}i:5;O:13:\"XMPPHP_XMLObj\":5:{s:4:\"name\";s:10:\"last_parse\";s:2:\"ns\";s:37:\"http:\/\/superfeedr.com\/xmpp-pubsub-ext\";s:5:\"attrs\";a:0:{}s:4:\"subs\";a:0:{}s:4:\"data\";s:25:\"2011-09-04T16:48:02+00:00\";}i:6;O:13:\"XMPPHP_XMLObj\":5:{s:4:\"name\";s:19:\"last_maintenance_at\";s:2:\"ns\";s:37:\"http:\/\/superfeedr.com\/xmpp-pubsub-ext\";s:5:\"attrs\";a:0:{}s:4:\"subs\";a:0:{}s:4:\"data\";s:25:\"2011-09-03T20:11:07+00:00\";}}s:4:\"data\";s:70:\"\n        \n        \n        \n        \n        \n        \n        \n      \";}}s:4:\"data\";s:12:\"\n      \n    \";}}s:4:\"data\";s:8:\"\n    \n  \";}}s:4:\"data\";s:4:\"\n  \n\";}"'));
        
        $adapter = new SubscriptionAdapter($xmpp);
        $adapter->setTimeout(30);
        
        // Order is important here
        $xmpp->expects($this->at(0))
                ->method('connectAndStartSession');
        $xmpp->expects($this->at(1))
                ->method('getId')
                ->will($this->returnValue(3));
        $xmpp->expects($this->at(2))
                ->method('addIdHandler')
                ->with(3, 'handleResponse', $this->isInstanceOf('Hearsay\SuperfeedrBundle\Subscription\SubscriptionAdapter'));
        $xmpp->expects($this->at(3))
                ->method('send')
                ->with($sent);
        $xmpp->expects($this->at(4))
                ->method('processUntil')
                ->with('handle_subscription', 30)
                ->will($this->returnCallback(function() use ($received, $adapter) {
                    $adapter->handleResponse($received);
                }));
        $xmpp->expects($this->once()) // This happens inside the processUntil call, so we can't give it an index
                ->method('event')
                ->with('handle_subscription');
        
        $success = $adapter->subscribeTo(array('http://superfeedr.com/dummy.xml'), true);
        $this->assertTrue($success, 'Expected subscription to be successful');
    }
}
