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

use Hearsay\SuperfeedrBundle\Subscription\Subscriber;

/**
 * Unit tests for the subscriber.
 * @author Kevin Montag <kevin@hearsay.it>
 */
class SubscriberTest extends \PHPUnit_Framework_TestCase
{
    public function testSubscriptionWithSingleResource()
    {
        $adapter = $this->getMock('Hearsay\SuperfeedrBundle\Subscription\SubscriptionAdapterInterface');
        $logger = $this->getMockBuilder('Hearsay\SuperfeedrBundle\Logger\SubscriptionLogger')
                ->disableOriginalConstructor()
                ->getMock();
        
        $adapter->expects($this->once())
                ->method('subscribeTo')
                ->with(array('http://www.google.com'), true)
                ->will($this->returnValue(true));
        $logger->expects($this->once())
                ->method('logSubscribeAttempt')
                ->with(true, array('http://www.google.com'), true);
        
        $subscriber = new Subscriber($adapter, $logger);
        $subscriber->subscribeTo('http://www.google.com', true);
    }
    
    public function testSubscriptionWithMultipleResources()
    {
        $adapter = $this->getMock('Hearsay\SuperfeedrBundle\Subscription\SubscriptionAdapterInterface');
        $logger = $this->getMockBuilder('Hearsay\SuperfeedrBundle\Logger\SubscriptionLogger')
                ->disableOriginalConstructor()
                ->getMock();
        
        $adapter->expects($this->once())
                ->method('subscribeTo')
                ->with(array('http://www.google.com', 'http://www.bing.com'), true)
                ->will($this->returnValue(false));
        $logger->expects($this->once())
                ->method('logSubscribeAttempt')
                ->with(false, array('http://www.google.com', 'http://www.bing.com'), true);
        
        $subscriber = new Subscriber($adapter, $logger);
        $subscriber->subscribeTo(array('http://www.google.com', 'http://www.bing.com'), true);
        
    }
    
    public function testUnsubscriptionWithSingleResource()
    {
        $adapter = $this->getMock('Hearsay\SuperfeedrBundle\Subscription\SubscriptionAdapterInterface');
        $logger = $this->getMockBuilder('Hearsay\SuperfeedrBundle\Logger\SubscriptionLogger')
                ->disableOriginalConstructor()
                ->getMock();
        
        $adapter->expects($this->once())
                ->method('unsubscribeFrom')
                ->with(array('http://www.google.com'))
                ->will($this->returnValue(true));
        $logger->expects($this->once())
                ->method('logUnsubscribeAttempt')
                ->with(true, array('http://www.google.com'));
        
        $subscriber = new Subscriber($adapter, $logger);
        $subscriber->unsubscribeFrom('http://www.google.com');        
    }
    
    public function testUnsubscriptionWithMultipleResources()
    {
        $adapter = $this->getMock('Hearsay\SuperfeedrBundle\Subscription\SubscriptionAdapterInterface');
        $logger = $this->getMockBuilder('Hearsay\SuperfeedrBundle\Logger\SubscriptionLogger')
                ->disableOriginalConstructor()
                ->getMock();
        
        $adapter->expects($this->once())
                ->method('unsubscribeFrom')
                ->with(array('http://www.google.com', 'http://www.bing.com'))
                ->will($this->returnValue(false));
        $logger->expects($this->once())
                ->method('logUnsubscribeAttempt')
                ->with(false, array('http://www.google.com', 'http://www.bing.com'));
        
        $subscriber = new Subscriber($adapter, $logger);
        $subscriber->unsubscribeFrom(array('http://www.google.com', 'http://www.bing.com'));
        
    }
}
