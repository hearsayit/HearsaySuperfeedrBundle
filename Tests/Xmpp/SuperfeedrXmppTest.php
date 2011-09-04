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
    
    /**
     * Make sure we fix the bug in the base XMPP class which prevents detection
     * of a complete buffer when several sister tags are present.
     */
    public function testSeveralXmlTagsAreCompleteBuffer()
    {
        // We use an actual problem buffer
        $xml = <<<XML
<iq from='firehoser.superfeedr.com' to='hearsayer@superfeedr.com/superfeedr' type='result' id='10'>
  <pubsub xmlns='http://jabber.org/protocol/pubsub'>
    <subscription node='http://feeds.feedburner.com/crunchgear' jid='hearsayer@superfeedr.com' subscription='subscribed'>
      <status xmlns='http://superfeedr.com/xmpp-pubsub-ext'>
        <http code='304'>Content not modified</http>
        <next_fetch>2011-06-13T06:42:37+00:00</next_fetch>
        <title>CrunchGear</title>
        <period>900</period>
        <last_fetch>2011-06-13T06:27:46+00:00</last_fetch>
        <last_parse>2011-06-12T15:38:52+00:00</last_parse>
        <last_maintenance_at>2011-06-11T13:13:42+00:00</last_maintenance_at>
      </status>
    </subscription>
  </pubsub>
</iq><message from='firehoser.superfeedr.com' to='hearsayer@superfeedr.com'>
  <event xmlns='http://jabber.org/protocol/pubsub#event'>
    <status xmlns='http://superfeedr.com/xmpp-pubsub-ext' feed='http://feeds.latimes.com/latimes/entertainment/'>
      <http code='200'>20175B in 0.285253228s, 5/10 new entries</http>
      <next_fetch>2011-06-13T19:03:27Z</next_fetch>
      <title>L.A. Times - Entertainment News</title>
      <subtitle>Headlines from latimes.com</subtitle>
      <entries_count_since_last_maintenance>24</entries_count_since_last_maintenance>
      <period>43200</period>
      <last_fetch>2011-06-13T06:34:21Z</last_fetch>
      <last_parse>2011-06-13T06:34:21Z</last_parse>
      <last_maintenance_at>2011-06-12T14:14:57+00:00</last_maintenance_at>
      <link href='http://www.latimes.com/entertainment/news/?track=rss' title='' type='text/html' rel='alternate'/>
      <link href='http://feeds.latimes.com/latimes/entertainment' title='' type='application/rss+xml' rel='self'/>
      <link href='http://pubsubhubbub.appspot.com/' title='' type='text/html' rel='hub'/>
    </status>
    <items node='http://feeds.latimes.com/latimes/entertainment/'>
      <item xmlns='http://jabber.org/protocol/pubsub'>
        <entry xmlns='http://www.w3.org/2005/Atom' xmlns:geo='http://www.georss.org/georss' xmlns:as='http://activitystrea.ms/spec/1.0/' xmlns:sf='http://superfeedr.com/xmpp-pubsub-ext' xml:lang='en'>
          <id>best-and-worst-of-the-2011-tony-awards-2011-06-13t04-28-46z</id>
          <published>2011-06-13T04:28:46Z</published>
          <updated>2011-06-13T04:28:46Z</updated>
          <title>Best and Worst of the 2011 Tony Awards</title>
          <summary type='html'>Neil Patrick Harris outdoes himself, Frances McDormand dresses down and more highlights and lowlights from the 2011 Tony Awards.
&lt;p&gt;&lt;a href=&quot;http://feedads.g.doubleclick.net/~at/EBCu_t4-KvQ3GVife-VeYOwfOZU/0/da&quot;&gt;&lt;img src=&quot;http://feedads.g.doubleclick.net/~at/EBCu_t4-KvQ3GVife-VeYOwfOZU/0/di&quot; border=&quot;0&quot; ismap=&quot;true&quot;&gt;&lt;/img&gt;&lt;/a&gt;&lt;br/&gt;
&lt;a href=&quot;http://feedads.g.doubleclick.net/~at/EBCu_t4-KvQ3GVife-VeYOwfOZU/1/da&quot;&gt;&lt;img src=&quot;http://feedads.g.doubleclick.net/~at/EBCu_t4-KvQ3GVife-VeYOwfOZU/1/di&quot; border=&quot;0&quot; ismap=&quot;true&quot;&gt;&lt;/img&gt;&lt;/a&gt;&lt;/p&gt;&lt;img src=&quot;http://feeds.feedburner.com/~r/latimes/entertainment/~4/M9ITKEJrcqI&quot; height=&quot;1&quot; width=&quot;1&quot;/&gt;</summary>
          <link rel='alternate' type='text/html' href='http://feeds.latimes.com/~r/latimes/entertainment/~3/M9ITKEJrcqI/env-best-worst-tonys-sl,0,5290583.storylink' title='Best and Worst of the 2011 Tony Awards'/>
          <link rel='thumbnail' type='image/jpeg' href='http://www.latimes.com/media/thumbnails/storylink/2011-06/62323375-12212809.jpg' title='Best and Worst of the 2011 Tony Awards'/>
          <link rel='enclosure' type='image/jpeg' href='http://www.latimes.com/media/alternatethumbnails/storylink/2011-06/62323375-12212810.jpg' title='Best and Worst of the 2011 Tony Awards'/>
          <link rel='alternate' type='text/html' href='http://www.latimes.com/entertainment/news/env-best-worst-tonys-sl,0,5290583.storylink?track=rss' title='Best and Worst of the 2011 Tony Awards'/>
        </entry>
      </item>
    </items>
  </event>
</message>
XML;
        $xmpp = new SuperfeedrXmpp('superfeedr.com', 5222, 'user', 'pass');
        $method = new \ReflectionMethod('Hearsay\SuperfeedrBundle\Xmpp\SuperfeedrXmpp', 'bufferComplete');
        $method->setAccessible(true);
        
        $xmpp->setExpensiveIterations(2);
        
        // Run the buffer through a few times without the expensive check
        $this->assertFalse($method->invoke($xmpp, $xml), 'Expected buffer to be incomplete without expensive check'); // No incomplete iterations
        $this->assertFalse($method->invoke($xmpp, $xml), 'Expected buffer to be incomplete without expensive check'); // First incomplete iteration
        $this->assertFalse($method->invoke($xmpp, $xml), 'Expected buffer to be incomplete without expensive check'); // Second incomplete iteration, next should trip the expensive check
        
        // Now run it with the expensive check and make sure it's registered as complete
        $this->assertTrue($method->invoke($xmpp, $xml), 'Expected buffer to be complete with expensive check');        
    }

    /**
     * Make sure we time out after the appropriate number of iterations with an
     * incomplete buffer that appears disconnected.
     */
    public function testTimeoutOnStuckBuffer()
    {
        // An incomplete buffer
        $xml = <<<XML
<iq from='firehoser.superfeedr.com' to='hearsayer@superfeedr.com/superfeedr' type='result' id='10'>
  <pubsub xmlns='http://jabber.org/protocol/pubsub'>
    <subscription node='http://feeds.feedburner.com/crunchgear' jid='hearsayer@superfeedr.com' subscription='subscribed'>
      <status xmlns='http://superfeedr.com/xmpp-pubsub-ext'>
        <http code='304'>Content not modified</http>
        <next_fetch>2011-06-13T06:42:37+00:00</next_fetch>
        <title>CrunchGear</title>
XML;
        $xmpp = new SuperfeedrXmpp('superfeedr.com', 5222, 'user', 'pass');
        $method = new \ReflectionMethod('Hearsay\SuperfeedrBundle\Xmpp\SuperfeedrXmpp', 'bufferComplete');
        $method->setAccessible(true);
        
        $xmpp->setMaxIncompleteIterations(2);
        
        // Run the buffer through a few times without the timeout check
        $this->assertFalse($method->invoke($xmpp, $xml), 'Expected buffer to be incomplete'); // No incomplete iterations
        $this->assertFalse($method->invoke($xmpp, $xml), 'Expected buffer to be incomplete'); // First incomplete iteration
        $this->assertFalse($method->invoke($xmpp, $xml), 'Expected buffer to be incomplete'); // Second incomplete iteration
        $this->assertFalse($method->invoke($xmpp, $xml), 'Expected buffer to be incomplete'); // Third incomplete iteration, so running count > max allowed; next should trip the timeout check
        
        $this->setExpectedException('Hearsay\SuperfeedrBundle\Exception\TimeoutException');
        $method->invoke($xmpp, $xml);
    }
}
