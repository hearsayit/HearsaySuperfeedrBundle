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

namespace Hearsay\SuperfeedrBundle\Tests\DataCollector;

use Hearsay\SuperfeedrBundle\DataCollector\SubscriptionDataCollector;
use Hearsay\SuperfeedrBundle\Logger\SubscribeAttempt;
use Hearsay\SuperfeedrBundle\Logger\UnsubscribeAttempt;

/**
 * Unit tests for the subscription data collector.
 * @author Kevin Montag <kevin@hearsay.it>
 */
class SubscriptionDataCollectorTest extends \PHPUnit_Framework_TestCase
{

    public function testLoggerParsed()
    {
        $subscribeAttempts = array(
            new SubscribeAttempt(true, array('http://www.google.com'), false),
            new SubscribeAttempt(true, array('http://www.bing.com'), false),
        );
        $unsubscribeAttempts = array(
            new UnsubscribeAttempt(true, array('http://www.google.com')),
            new UnsubscribeAttempt(true, array('http://www.bing.com')),
            new UnsubscribeAttempt(false, array('http://www.yahoo.com')),
            
        );
        
        $logger = $this->getMockBuilder('Hearsay\SuperfeedrBundle\Logger\SubscriptionLogger')
                ->disableOriginalConstructor()
                ->getMock();
        $logger->expects($this->once())
                ->method('countSubscribeAttempts')
                ->will($this->returnValue(2));
        $logger->expects($this->once())
                ->method('getSubscribeAttempts')
                ->will($this->returnValue($subscribeAttempts));
        $logger->expects($this->once())
                ->method('countUnsubscribeAttempts')
                ->will($this->returnValue(3));
        $logger->expects($this->once())
                ->method('getUnsubscribeAttempts')
                ->will($this->returnValue($unsubscribeAttempts));

        $collector = new SubscriptionDataCollector($logger);
        
        $request = $this->getMock('Symfony\Component\HttpFoundation\Request');
        $request->expects($this->never())
                ->method($this->anything());
        $response = $this->getMock('Symfony\Component\HttpFoundation\Response');
        $response->expects($this->never())
                ->method($this->anything());
        
        $collector->collect($request, $response);
        
        $this->assertEquals(2, $collector->getSubscribeAttemptCount(), 'Incorrect number of subscribe attempts');
        $this->assertEquals($subscribeAttempts, $collector->getSubscribeAttempts(), 'Incorrect subscribe attempt data');
        $this->assertEquals(3, $collector->getUnsubscribeAttemptCount(), 'Incorrect number of unsubscribe attempts');
        $this->assertEquals($unsubscribeAttempts, $collector->getUnsubscribeAttempts(), 'Incorrect unsubscribe attempt data');
    }

}
