<?php

/*
 * Copyright (c) 2011 Hearsay News Products, Inc.
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy 
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace Hearsay\SuperfeedrBundle\Tests\Xmpp;

use Hearsay\SuperfeedrBundle\Xmpp\Receiver;
use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * Unit tests for the message receiver.
 * @author Kevin Montag <kevin@hearsay.it>
 */
class ReceiverTest extends WebTestCase
{

    /**
     * Make sure we can receive messages and send their data to our receiver.
     * @covers Hearsay\SuperfeedrBundle\Xmpp\Receiver
     * @covers Hearsay\SuperfeedrBundle\Handler\HandlerInterface
     */
    public function testMessageHandled()
    {
        // We use an actual received message
        $message = <<<MES
<message from='firehoser.superfeedr.com' to='kmontag@superfeedr.com'>
  <event xmlns='http://jabber.org/protocol/pubsub#event'>
    <status xmlns='http://superfeedr.com/xmpp-pubsub-ext' feed='http://superfeedr.com/dummy.xml'>
      <http code='200'>7452B in 3.031444853s, 1/10 new entries</http>
      <next_fetch>2011-06-08T21:05:43Z</next_fetch>
      <title>The Dummy Time Feed</title>
      <entries_count_since_last_maintenance>74</entries_count_since_last_maintenance>
      <period>3600</period>
      <last_fetch>2011-06-08T20:06:11Z</last_fetch>
      <last_parse>2011-06-08T20:06:11Z</last_parse>
      <last_maintenance_at>2011-06-06T07:15:11+00:00</last_maintenance_at>
      <link href='http://superfeedr.com' title='' type='text/html' rel='alternate'/>
      <link href='http://superfeedr.com/dummy.xml' title='' type='application/atom+xml' rel='self'/>
    </status>
    <items node='http://superfeedr.com/dummy.xml'>
      <item xmlns='http://jabber.org/protocol/pubsub'>
        <entry xmlns='http://www.w3.org/2005/Atom' xmlns:geo='http://www.georss.org/georss' xmlns:as='http://activitystrea.ms/spec/1.0/' xmlns:sf='http://superfeedr.com/xmpp-pubsub-ext' xml:lang='en-US'>
          <id>tag:superfeedr.com,2005:String/1307563668</id>
          <published>2011-06-08T20:07:48+00:00</published>
          <updated>2011-06-08T20:07:48+00:00</updated>
          <title>20:07:48</title>
          <summary type='text'/>
          <content type='text'>Wednesday June 08 20:07:48 UTC 2011 Somebody wanted to know what time it was.</content>
          <geo:point>37.773721,-122.414957</geo:point>
          <link rel='alternate' type='text/html' href='http://superfeedr.com/?1307563668' title='20:07:48'/>
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
</message>
MES;
        $payload = simplexml_load_string($message)->event->asXml();
        
        // Sanity check for the payload
        $this->assertTrue(\strpos($payload, '<event ') === 0);
        
        $jaxlFactory = $this->getMockBuilder('Hearsay\SuperfeedrBundle\Xmpp\JaxlFactory')
                ->disableOriginalConstructor()
                ->getMock();
        $jaxl = $this->getMockBuilder('Hearsay\SuperfeedrBundle\Xmpp\Jaxl')
                ->disableOriginalConstructor()
                ->getMock();
        
        $handler = $this->getMock('Hearsay\SuperfeedrBundle\Handler\HandlerInterface');
        $logger = $this->getMock('Symfony\Component\HttpKernel\Log\LoggerInterface');
        
        $receiver = new Receiver($jaxlFactory, $handler, $logger);
        
        $handler->expects($this->once())
                ->method('handleNotification')
                ->with($payload);
        
        $receiver->handlePost($message, $jaxl);
    }
    
    /**
     * Make sure we don't have any problem parsing non-messages.
     * @covers Hearsay\SuperfeedrBundle\Xmpp\Receiver
     */
    public function testNonMessageNotHandled()
    {
        $iq = <<<XML
<iq id='6' type='result'>
    <bind xmlns='urn:ietf:params:xml:ns:xmpp-bind'>
        <jid>kmontag@superfeedr.com/jaxl.2.1307563481</jid>
    </bind>
</iq>
XML;
        $jaxlFactory = $this->getMockBuilder('Hearsay\SuperfeedrBundle\Xmpp\JaxlFactory')
                ->disableOriginalConstructor()
                ->getMock();
        $jaxl = $this->getMockBuilder('Hearsay\SuperfeedrBundle\Xmpp\Jaxl')
                ->disableOriginalConstructor()
                ->getMock();
        
        $handler = $this->getMock('Hearsay\SuperfeedrBundle\Handler\HandlerInterface');
        $logger = $this->getMock('Symfony\Component\HttpKernel\Log\LoggerInterface');
        
        $receiver = new Receiver($jaxlFactory, $handler, $logger);
        
        $handler->expects($this->never())
                ->method('handleNotification');
        
        $receiver->handlePost($iq, $jaxl);        
    }
    
    /**
     * Make sure we silently ignore handling of malformed XML.
     * @covers Hearsay\SuperfeedrBundle\Xmpp\Receiver
     */
    public function testBadXmlNotHandled()
    {
        $stream = <<<XML
<?xml version='1.0'?>
<stream:stream xmlns='jabber:client' xmlns:stream='http://etherx.jabber.org/streams' id='1259898665' from='superfeedr.com' version='1.0' xml:lang='en'>
XML;
        $jaxlFactory = $this->getMockBuilder('Hearsay\SuperfeedrBundle\Xmpp\JaxlFactory')
                ->disableOriginalConstructor()
                ->getMock();
        $jaxl = $this->getMockBuilder('Hearsay\SuperfeedrBundle\Xmpp\Jaxl')
                ->disableOriginalConstructor()
                ->getMock();
        
        $handler = $this->getMock('Hearsay\SuperfeedrBundle\Handler\HandlerInterface');
        $logger = $this->getMock('Symfony\Component\HttpKernel\Log\LoggerInterface');
        
        $receiver = new Receiver($jaxlFactory, $handler, $logger);
        
        $handler->expects($this->never())
                ->method('handleNotification');
        
        $receiver->handlePost($stream, $jaxl);
    }

}
