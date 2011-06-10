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

namespace Hearsay\SuperfeedrBundle\Tests\Handler;

use Hearsay\SuperfeedrBundle\Events;
use Hearsay\SuperfeedrBundle\Event\NotificationReceivedEvent;
use Hearsay\SuperfeedrBundle\Handler\EventHandler;
use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * Unit and functional tests for the event-dispatching notification handler.
 * @author Kevin Montag <kevin@hearsay.it>
 */
class EventHandlerTest extends WebTestCase
{
    /**
     * Make sure we can properly parse a message and dispatch a related event.
     * @covers Hearsay\SuperfeedrBundle\Events
     * @covers Hearsay\SuperfeedrBundle\Event\NotificationReceivedEvent
     * @covers Hearsay\SuperfeedrBundle\Handler\EventHandler
     */
    public function testNotificationParsed()
    {
        $payload = <<<PAY
  <event xmlns='http://jabber.org/protocol/pubsub#event'>
    <status xmlns='http://superfeedr.com/xmpp-pubsub-ext' feed='http://superfeedr.com/dummy.xml'>
      <http code='200'>7452B in 0.029085429s, 4/10 new entries</http>
      <next_fetch>2011-06-08T21:06:33Z</next_fetch>
      <title>The Dummy Time Feed</title>
      <entries_count_since_last_maintenance>77</entries_count_since_last_maintenance>
      <period>3600</period>
      <last_fetch>2011-06-08T20:05:05Z</last_fetch>
      <last_parse>2011-06-08T20:05:05Z</last_parse>
      <last_maintenance_at>2011-06-06T07:15:11+00:00</last_maintenance_at>
      <link href='http://superfeedr.com' title='' type='text/html' rel='alternate'/>
      <link href='http://superfeedr.com/dummy.xml' title='' type='application/atom+xml' rel='self'/>
    </status>
    <items node='http://superfeedr.com/dummy.xml'>
      <item xmlns='http://jabber.org/protocol/pubsub'>
        <entry xmlns='http://www.w3.org/2005/Atom' xmlns:geo='http://www.georss.org/georss' xmlns:as='http://activitystrea.ms/spec/1.0/' xmlns:sf='http://superfeedr.com/xmpp-pubsub-ext' xml:lang='en-US'>
          <id>tag:superfeedr.com,2005:String/1307563608</id>
          <published>2011-06-08T20:06:48+00:00</published>
          <updated>2011-06-08T20:06:48+00:00</updated>
          <title>20:06:48</title>
          <summary type='text'/>
          <content type='text'>Wednesday June 08 20:06:48 UTC 2011 Somebody wanted to know what time it was.</content>
          <geo:point>37.773721,-122.414957</geo:point>
          <link rel='alternate' type='text/html' href='http://superfeedr.com/?1307563608' title='20:06:48'/>
          <category term='tests'/>
          <author>
            <name>Superfeedr</name>
            <uri>http://superfeedr.com/</uri>
            <email>julien@superfeedr.com</email>
          </author>
        </entry>
      </item>
      <item xmlns='http://jabber.org/protocol/pubsub'>
        <entry xmlns='http://www.w3.org/2005/Atom' xmlns:geo='http://www.georss.org/georss' xmlns:as='http://activitystrea.ms/spec/1.0/' xmlns:sf='http://superfeedr.com/xmpp-pubsub-ext' xml:lang='en-US'>
          <id>tag:superfeedr.com,2005:String/1307563604</id>
          <published>2011-06-08T20:06:44+00:00</published>
          <updated>2011-06-08T20:06:44+00:00</updated>
          <title>20:06:44</title>
          <summary type='text'/>
          <content type='text'>Wednesday June 08 20:06:44 UTC 2011 Somebody wanted to know what time it was.</content>
          <geo:point>37.773721,-122.414957</geo:point>
          <link rel='alternate' type='text/html' href='http://superfeedr.com/?1307563604' title='20:06:44'/>
          <category term='tests'/>
          <author>
            <name>Superfeedr</name>
            <uri>http://superfeedr.com/</uri>
            <email>julien@superfeedr.com</email>
          </author>
        </entry>
      </item>
      <item xmlns='http://jabber.org/protocol/pubsub'>
        <entry xmlns='http://www.w3.org/2005/Atom' xmlns:geo='http://www.georss.org/georss' xmlns:as='http://activitystrea.ms/spec/1.0/' xmlns:sf='http://superfeedr.com/xmpp-pubsub-ext' xml:lang='en-US'>
          <id>tag:superfeedr.com,2005:String/1307563599</id>
          <published>2011-06-08T20:06:39+00:00</published>
          <updated>2011-06-08T20:06:39+00:00</updated>
          <title>20:06:39</title>
          <summary type='text'/>
          <content type='text'>Wednesday June 08 20:06:39 UTC 2011 Somebody wanted to know what time it was.</content>
          <geo:point>37.773721,-122.414957</geo:point>
          <link rel='alternate' type='text/html' href='http://superfeedr.com/?1307563599' title='20:06:39'/>
          <category term='tests'/>
          <author>
            <name>Superfeedr</name>
            <uri>http://superfeedr.com/</uri>
            <email>julien@superfeedr.com</email>
          </author>
        </entry>
      </item>
      <item xmlns='http://jabber.org/protocol/pubsub'>
        <entry xmlns='http://www.w3.org/2005/Atom' xmlns:geo='http://www.georss.org/georss' xmlns:as='http://activitystrea.ms/spec/1.0/' xmlns:sf='http://superfeedr.com/xmpp-pubsub-ext' xml:lang='en-US'>
          <id>tag:superfeedr.com,2005:String/1307563598</id>
          <published>2011-06-08T20:06:38+00:00</published>
          <updated>2011-06-08T20:06:38+00:00</updated>
          <title>20:06:38</title>
          <summary type='text'/>
          <content type='text'>Wednesday June 08 20:06:38 UTC 2011 Somebody wanted to know what time it was.</content>
          <geo:point>37.773721,-122.414957</geo:point>
          <link rel='alternate' type='text/html' href='http://superfeedr.com/?1307563598' title='20:06:38'/>
          <category term='tests'/>
          <author>
            <name>Superfeedr</name>
            <uri>http://superfeedr.com/</uri>
            <email>julien@superfeedr.com</email>
          </author>
        </entry>
      </item>
    </items>
  </event>
PAY;
        $dispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $handler = new EventHandler($dispatcher);
        
        $dispatcher->expects($this->once())
                ->method('dispatch')
                ->will($this->returnCallback(array($this, 'handleEventForTestNotificationParsed')));
        
        $handler->handleNotification($payload);
    }
    
    /**
     * Helper callback for ensuring that a message is properly parsed, as part
     * of the <code>testNotificationParsed</code> test.
     * @param string $eventName The event name.
     * @param NotificationReceivedEvent $event The built event.
     */
    public function handleEventForTestNotificationParsed($eventName, NotificationReceivedEvent $event)
    {
        $this->assertEquals(Events::NOTIFICATION_RECEIVED, $eventName);
        $this->assertFalse($event->isDigest());
        $this->assertEquals('http://superfeedr.com/dummy.xml', $event->getResourceUrl());
        $this->assertEquals(4, \count($event->getItems()));
        
        $expected = <<<EXP
<entry xmlns='http://www.w3.org/2005/Atom' xmlns:geo='http://www.georss.org/georss' xmlns:as='http://activitystrea.ms/spec/1.0/' xmlns:sf='http://superfeedr.com/xmpp-pubsub-ext' xml:lang='en-US'>
          <id>tag:superfeedr.com,2005:String/1307563599</id>
          <published>2011-06-08T20:06:39+00:00</published>
          <updated>2011-06-08T20:06:39+00:00</updated>
          <title>20:06:39</title>
          <summary type='text'/>
          <content type='text'>Wednesday June 08 20:06:39 UTC 2011 Somebody wanted to know what time it was.</content>
          <geo:point>37.773721,-122.414957</geo:point>
          <link rel='alternate' type='text/html' href='http://superfeedr.com/?1307563599' title='20:06:39'/>
          <category term='tests'/>
          <author>
            <name>Superfeedr</name>
            <uri>http://superfeedr.com/</uri>
            <email>julien@superfeedr.com</email>
          </author>
        </entry>        
EXP;
        $items = $event->getItems();
        $this->assertXmlStringEqualsXmlString($expected, $items[2]);
    }
    
    /**
     * Make sure we can properly parse digest events.
     * @covers Hearsay\SuperfeedrBundle\Events
     * @covers Hearsay\SuperfeedrBundle\Event\NotificationReceivedEvent
     * @covers Hearsay\SuperfeedrBundle\Handler\EventHandler
     */
    public function testDigestParsed()
    {
        // Use a sample payload from the Superfeedr documentation
        $payload = <<<XML
 <event xmlns="http://jabber.org/protocol/pubsub#event">
  <status feed="http://domain.tld/feed.xml" xmlns="http://superfeedr.com/xmpp-pubsub-ext" digest="true">
   <http code="200">9718 bytes fetched in 1.462708s : 2 new entries.</http>
   <next_fetch>2009-05-10T11:19:38-07:00</next_fetch>
   <title>Lorem Ipsum</title>
  </status>
  <items node="http://domain.tld/feed.xml">
   <item >
    <entry xmlns="http://www.w3.org/2005/Atom">
     <title>Soliloquy</title>
     <summary>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</summary>
     <link rel="alternate" type="text/html" href="http://superfeedr.com/entries/12345789"/>
     <id>tag:domain.tld,2009:Soliloquy-32397</id>
     <published>2010-04-05T11:04:21Z</published>
    </entry>
   </item>
   <item>
    <entry xmlns="http://www.w3.org/2005/Atom">
     <title>Finibus Bonorum et Malorum</title>
     <summary>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt.</summary>
     <link rel="alternate" type="text/html" href="http://superfeedr.com/entries/12345788"/>
     <id>tag:domain.tld,2009:Finibus-32398</id>
     <published>2010-04-06T08:54:02Z</published>
    </entry>
   </item>
  </items>
 </event>
XML;
        
        $dispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $handler = new EventHandler($dispatcher);
        
        $dispatcher->expects($this->once())
                ->method('dispatch')
                ->will($this->returnCallback(array($this, 'handleEventForTestDigestParsed')));
        
        $handler->handleNotification($payload);
    }

    /**
     * Helper callback to receive event notifications for the
     * <code>testDigestParsed</code> function.
     * @param string $eventName Event name.
     * @param NotificationReceivedEvent $event The event.
     */
    public function handleEventForTestDigestParsed($eventName, NotificationReceivedEvent $event)
    {
        $this->assertTrue($event->isDigest());
    }
}
