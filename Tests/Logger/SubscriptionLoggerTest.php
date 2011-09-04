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

namespace Hearsay\SuperfeedrBundle\Tests\Logger;

use Hearsay\SuperfeedrBundle\Logger\SubscriptionLogger;

/**
 * Unit tests for the subscription attempt logger.
 * @author Kevin Montag <kevin@hearsay.it>
 */
class SubscriptionLoggerTest extends \PHPUnit_Framework_TestCase
{
    public function testSuccessfulSubscribeAttemptLogged()
    {
        $logger = $this->getMock('Symfony\Component\HttpKernel\Log\LoggerInterface');
        $subscriptionLogger = new SubscriptionLogger($logger);
        
        $logger->expects($this->exactly(2))
                ->method('info');
        $subscriptionLogger->logSubscribeAttempt(true, array('http://www.google.com'), true);
        $subscriptionLogger->logSubscribeAttempt(true, array('http://www.bing.com'), false);
        
        $this->assertEquals(2, $subscriptionLogger->countSubscribeAttempts(), 'Incorrect number of attempts logged');
        
        $attempts = $subscriptionLogger->getSubscribeAttempts();
        $this->assertTrue($attempts[0]->successful, 'Attempt expected to be successful');
        $this->assertEquals(array('http://www.google.com'), $attempts[0]->urls, 'Incorrect URLs stored for attempt');
        $this->assertTrue($attempts[0]->digest, 'Attempt expected to be digest subscription');
        
        $this->assertTrue($attempts[1]->successful, 'Attempt expected to be successful');
        $this->assertEquals(array('http://www.bing.com'), $attempts[1]->urls, 'Incorrect URLs stored for attempt');
        $this->assertFalse($attempts[1]->digest, 'Attempt not expected to be digest subscription');
    }
    
    public function testUnsuccessfulSubscribeAttemptLogged()
    {
        $logger = $this->getMock('Symfony\Component\HttpKernel\Log\LoggerInterface');
        $subscriptionLogger = new SubscriptionLogger($logger);
        
        $logger->expects($this->once())
                ->method('warn');
        $subscriptionLogger->logSubscribeAttempt(false, array('http://www.google.com'), true);
        
        $this->assertEquals(1, $subscriptionLogger->countSubscribeAttempts(), 'Incorrect number of attempts logged');
        $attempts = $subscriptionLogger->getSubscribeAttempts();
        $this->assertFalse($attempts[0]->successful, 'Attempt expected to be unsuccessful');
        $this->assertEquals(array('http://www.google.com'), $attempts[0]->urls, 'Incorrect URLs stored for attempt');
        $this->assertTrue($attempts[0]->digest, 'Attempt expected to be digest subscription');
    }
    
    public function testSuccessfulUnsubscribeAttemptLogged()
    {
        $logger = $this->getMock('Symfony\Component\HttpKernel\Log\LoggerInterface');
        $subscriptionLogger = new SubscriptionLogger($logger);
        
        $logger->expects($this->exactly(2))
                ->method('info');
        $subscriptionLogger->logUnsubscribeAttempt(true, array('http://www.google.com'));
        $subscriptionLogger->logUnsubscribeAttempt(true, array('http://www.bing.com'));
        
        $this->assertEquals(2, $subscriptionLogger->countUnsubscribeAttempts(), 'Incorrect number of attempts logged');
        
        $attempts = $subscriptionLogger->getUnsubscribeAttempts();
        $this->assertTrue($attempts[0]->successful, 'Attempt expected to be successful');
        $this->assertEquals(array('http://www.google.com'), $attempts[0]->urls, 'Incorrect URLs stored for attempt');
        
        $this->assertTrue($attempts[1]->successful, 'Attempt expected to be successful');
        $this->assertEquals(array('http://www.bing.com'), $attempts[1]->urls, 'Incorrect URLs stored for attempt');
    }
    
    public function testUnsuccessfulUnsubscribeAttemptLogged()
    {
        $logger = $this->getMock('Symfony\Component\HttpKernel\Log\LoggerInterface');
        $subscriptionLogger = new SubscriptionLogger($logger);
        
        $logger->expects($this->once())
                ->method('warn');
        $subscriptionLogger->logUnsubscribeAttempt(false, array('http://www.google.com'));
        
        $this->assertEquals(1, $subscriptionLogger->countUnsubscribeAttempts(), 'Incorrect number of attempts logged');
        $attempts = $subscriptionLogger->getUnsubscribeAttempts();
        $this->assertFalse($attempts[0]->successful, 'Attempt expected to be unsuccessful');
        $this->assertEquals(array('http://www.google.com'), $attempts[0]->urls, 'Incorrect URLs stored for attempt');
        
    }
}
