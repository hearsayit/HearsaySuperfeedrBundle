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

use Hearsay\SuperfeedrBundle\Xmpp\Superfeedr;
use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * Tests for the Superfeedr connection.
 * @author Kevin Montag <kevin@hearsay.it>
 */
class SuperfeedrTest extends WebTestCase
{

    /**
     * Get the configured Superfeedr service.
     * @return Superfeedr The Superfeedr connection.
     */
    protected function getSuperfeedr()
    {
        return $this->getContainer()->get('hearsay_superfeedr.superfeedr');
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        // We can't do anything without an internet connection
        try {
            \fsockopen('www.google.com', 80);
        } catch (\Exception $e) {
            $this->markTestSkipped("Can't test XMPP without an internet connection.");
        }
    }

    /**
     * Make sure we can subscribe to, and then unsubscribe from, a feed via
     * Superfeedr.  Makes a live request to the Superfeedr server.
     * @covers Hearsay\SuperfeedrBundle\Xmpp\Superfeedr
     */
    public function testSubscriptionAndUnsubscriptionPossible()
    {
        $subscriber = $this->getSuperfeedr();
        $this->assertTrue($subscriber->subscribeTo('http://superfeedr.com/dummy.xml', true));
        $this->assertTrue($subscriber->unsubscribeFrom('http://superfeedr.com/dummy.xml'));
        $subscriber->disconnect();
    }

    /**
     * Make sure we can't subscribe to nonexistent resources.  Makes a live
     * request to the Superfeedr server.
     * @covers Hearsay\SuperfeedrBundle\Xmpp\Superfeedr
     */
    public function testBadSubscriptionNotPossible()
    {
        $subscriber = $this->getSuperfeedr();
        $this->assertFalse($subscriber->subscribeTo('not a url at all', false));
        $subscriber->disconnect();
    }
    
    /**
     * Make sure we can properly recognize complete buffers which are not
     * otherwise recognized by the base XMPP class, in particular buffers
     * consisting of two sister XML tags.
     * @covers Hearsay\SuperfeedrBundle\Xmpp\Superfeedr
     */
    public function testNonstandardBufferComplete()
    {
        $superfeedr = new Superfeedr('user', 'pass');
        $method = new \ReflectionMethod('Hearsay\SuperfeedrBundle\Xmpp\Superfeedr', 'bufferComplete');
        $method->setAccessible(true);
        
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
        // Run the buffer through a few times
        $this->assertFalse($method->invoke($superfeedr, $xml, 2));
        $this->assertFalse($method->invoke($superfeedr, $xml, 2));
        $this->assertFalse($method->invoke($superfeedr, $xml, 2));
        
        // Now pass in the whole thing and make sure we can process it
        $this->assertTrue($method->invoke($superfeedr, $xml, 2));
    }

    /**
     * Make sure we can receive messages and send their data to our receiver.
     * @covers Hearsay\SuperfeedrBundle\Xmpp\Superfeedr
     * @covers Hearsay\SuperfeedrBundle\Handler\HandlerInterface
     */
    public function testMessageHandled()
    {
        // We use an actual received message
        $serialized = <<<SER
O:13:"XMPPHP_XMLObj":5:{s:4:"name";s:7:"message";s:2:"ns";s:13:"jabber:client";s:5:"attrs";a:2:{s:4:"from";s:24:"firehoser.superfeedr.com";s:2:"to";s:24:"hearsayer@superfeedr.com";}s:4:"subs";a:1:{i:0;O:13:"XMPPHP_XMLObj":5:{s:4:"name";s:5:"event";s:2:"ns";s:39:"http://jabber.org/protocol/pubsub#event";s:5:"attrs";a:1:{s:5:"xmlns";s:39:"http://jabber.org/protocol/pubsub#event";}s:4:"subs";a:2:{i:0;O:13:"XMPPHP_XMLObj":5:{s:4:"name";s:6:"status";s:2:"ns";s:37:"http://superfeedr.com/xmpp-pubsub-ext";s:5:"attrs";a:2:{s:5:"xmlns";s:37:"http://superfeedr.com/xmpp-pubsub-ext";s:4:"feed";s:31:"http://star-wars.alltop.com/rss";}s:4:"subs";a:11:{i:0;O:13:"XMPPHP_XMLObj":5:{s:4:"name";s:4:"http";s:2:"ns";s:37:"http://superfeedr.com/xmpp-pubsub-ext";s:5:"attrs";a:1:{s:4:"code";s:3:"200";}s:4:"subs";a:0:{}s:4:"data";s:41:"74972B in 1.117581297s, 2/100 new entries";}i:1;O:13:"XMPPHP_XMLObj":5:{s:4:"name";s:10:"next_fetch";s:2:"ns";s:37:"http://superfeedr.com/xmpp-pubsub-ext";s:5:"attrs";a:0:{}s:4:"subs";a:0:{}s:4:"data";s:20:"2011-06-10T07:53:29Z";}i:2;O:13:"XMPPHP_XMLObj":5:{s:4:"name";s:5:"title";s:2:"ns";s:37:"http://superfeedr.com/xmpp-pubsub-ext";s:5:"attrs";a:0:{}s:4:"subs";a:0:{}s:4:"data";s:10:"Alltop RSS";}i:3;O:13:"XMPPHP_XMLObj":5:{s:4:"name";s:8:"subtitle";s:2:"ns";s:37:"http://superfeedr.com/xmpp-pubsub-ext";s:5:"attrs";a:0:{}s:4:"subs";a:0:{}s:4:"data";s:40:"Alltop RSS feed for star-wars.alltop.com";}i:4;O:13:"XMPPHP_XMLObj":5:{s:4:"name";s:36:"entries_count_since_last_maintenance";s:2:"ns";s:37:"http://superfeedr.com/xmpp-pubsub-ext";s:5:"attrs";a:0:{}s:4:"subs";a:0:{}s:4:"data";s:2:"53";}i:5;O:13:"XMPPHP_XMLObj":5:{s:4:"name";s:6:"period";s:2:"ns";s:37:"http://superfeedr.com/xmpp-pubsub-ext";s:5:"attrs";a:0:{}s:4:"subs";a:0:{}s:4:"data";s:3:"225";}i:6;O:13:"XMPPHP_XMLObj":5:{s:4:"name";s:10:"last_fetch";s:2:"ns";s:37:"http://superfeedr.com/xmpp-pubsub-ext";s:5:"attrs";a:0:{}s:4:"subs";a:0:{}s:4:"data";s:20:"2011-06-10T07:49:38Z";}i:7;O:13:"XMPPHP_XMLObj":5:{s:4:"name";s:10:"last_parse";s:2:"ns";s:37:"http://superfeedr.com/xmpp-pubsub-ext";s:5:"attrs";a:0:{}s:4:"subs";a:0:{}s:4:"data";s:20:"2011-06-10T07:49:38Z";}i:8;O:13:"XMPPHP_XMLObj":5:{s:4:"name";s:19:"last_maintenance_at";s:2:"ns";s:37:"http://superfeedr.com/xmpp-pubsub-ext";s:5:"attrs";a:0:{}s:4:"subs";a:0:{}s:4:"data";s:25:"2011-06-09T22:31:47+00:00";}i:9;O:13:"XMPPHP_XMLObj":5:{s:4:"name";s:4:"link";s:2:"ns";s:37:"http://superfeedr.com/xmpp-pubsub-ext";s:5:"attrs";a:4:{s:4:"href";s:27:"http://star-wars.alltop.com";s:5:"title";s:0:"";s:4:"type";s:9:"text/html";s:3:"rel";s:9:"alternate";}s:4:"subs";a:0:{}s:4:"data";s:0:"";}i:10;O:13:"XMPPHP_XMLObj":5:{s:4:"name";s:4:"link";s:2:"ns";s:37:"http://superfeedr.com/xmpp-pubsub-ext";s:5:"attrs";a:4:{s:4:"href";s:32:"http://star-wars.alltop.com/rss/";s:5:"title";s:0:"";s:4:"type";s:19:"application/rss+xml";s:3:"rel";s:4:"self";}s:4:"subs";a:0:{}s:4:"data";s:0:"";}}s:4:"data";s:82:"
      
      
      
      
      
      
      
      
      
      
      
    ";}i:1;O:13:"XMPPHP_XMLObj":5:{s:4:"name";s:5:"items";s:2:"ns";s:39:"http://jabber.org/protocol/pubsub#event";s:5:"attrs";a:1:{s:4:"node";s:31:"http://star-wars.alltop.com/rss";}s:4:"subs";a:2:{i:0;O:13:"XMPPHP_XMLObj":5:{s:4:"name";s:4:"item";s:2:"ns";s:33:"http://jabber.org/protocol/pubsub";s:5:"attrs";a:1:{s:5:"xmlns";s:33:"http://jabber.org/protocol/pubsub";}s:4:"subs";a:1:{i:0;O:13:"XMPPHP_XMLObj":5:{s:4:"name";s:5:"entry";s:2:"ns";s:27:"http://www.w3.org/2005/Atom";s:5:"attrs";a:5:{s:5:"xmlns";s:27:"http://www.w3.org/2005/Atom";s:9:"xmlns:geo";s:28:"http://www.georss.org/georss";s:8:"xmlns:as";s:33:"http://activitystrea.ms/spec/1.0/";s:8:"xmlns:sf";s:37:"http://superfeedr.com/xmpp-pubsub-ext";s:8:"xml:lang";s:5:"en-us";}s:4:"subs";a:6:{i:0;O:13:"XMPPHP_XMLObj":5:{s:4:"name";s:2:"id";s:2:"ns";s:27:"http://www.w3.org/2005/Atom";s:5:"attrs";a:0:{}s:4:"subs";a:0:{}s:4:"data";s:183:"http://news.google.com/news/url?sa=t&fd=R&usg=AFQjCNHDGvGv2tSTYEcYM21n2U2T6TFfKA&url=http://www.denofgeek.com/television/933690/what_is_happening_with_star_wars_liveaction_series.html";}i:1;O:13:"XMPPHP_XMLObj":5:{s:4:"name";s:9:"published";s:2:"ns";s:27:"http://www.w3.org/2005/Atom";s:5:"attrs";a:0:{}s:4:"subs";a:0:{}s:4:"data";s:25:"2011-06-10T07:49:38+00:00";}i:2;O:13:"XMPPHP_XMLObj":5:{s:4:"name";s:7:"updated";s:2:"ns";s:27:"http://www.w3.org/2005/Atom";s:5:"attrs";a:0:{}s:4:"subs";a:0:{}s:4:"data";s:25:"2011-06-10T07:49:38+00:00";}i:3;O:13:"XMPPHP_XMLObj":5:{s:4:"name";s:5:"title";s:2:"ns";s:27:"http://www.w3.org/2005/Atom";s:5:"attrs";a:0:{}s:4:"subs";a:0:{}s:4:"data";s:66:"What is happening with Star Wars live-action series? - Den Of Geek";}i:4;O:13:"XMPPHP_XMLObj":5:{s:4:"name";s:7:"summary";s:2:"ns";s:27:"http://www.w3.org/2005/Atom";s:5:"attrs";a:1:{s:4:"type";s:4:"html";}s:4:"subs";a:0:{}s:4:"data";s:892:"<table border="0" cellpadding="2" cellspacing="7"><tr><td width="80" align="center" valign="top"></td><td valign="top"><br /><div><img alt="" height="1" width="1" /></div><div><a href="http://news.google.com/news/url?sa=t&amp;fd=R&amp;usg=AFQjCNHDGvGv2tSTYEcYM21n2U2T6TFfKA&amp;url=http://www.denofgeek.com/television/933690/what_is_happening_with_star_wars_liveaction_series.html"><b>What is happening with <b>Star Wars</b> live-action series?</b></a><br /><b>Den Of Geek</b><br />First mentioned by George Lucas in the promotional tours for Revenge Of The Sith back in 2005, the <b>Star Wars</b> live-action television series has sort of dipped off the radar since being trumpeted some years ago. Various snippets were released over the <b>...</b><br /><br /><a href="http://news.google.com/news/more?pz=1&amp;ned=us&amp;ncl=dHN3ZeSthBtbokM"><nobr><b></b></nobr></a></div></td></tr></table>";}i:5;O:13:"XMPPHP_XMLObj":5:{s:4:"name";s:4:"link";s:2:"ns";s:27:"http://www.w3.org/2005/Atom";s:5:"attrs";a:4:{s:3:"rel";s:9:"alternate";s:4:"type";s:9:"text/html";s:4:"href";s:183:"http://news.google.com/news/url?sa=t&fd=R&usg=AFQjCNHDGvGv2tSTYEcYM21n2U2T6TFfKA&url=http://www.denofgeek.com/television/933690/what_is_happening_with_star_wars_liveaction_series.html";s:5:"title";s:66:"What is happening with Star Wars live-action series? - Den Of Geek";}s:4:"subs";a:0:{}s:4:"data";s:0:"";}}s:4:"data";s:75:"
          
          
          
          
          
          
        ";}}s:4:"data";s:16:"
        
      ";}i:1;O:13:"XMPPHP_XMLObj":5:{s:4:"name";s:4:"item";s:2:"ns";s:33:"http://jabber.org/protocol/pubsub";s:5:"attrs";a:1:{s:5:"xmlns";s:33:"http://jabber.org/protocol/pubsub";}s:4:"subs";a:1:{i:0;O:13:"XMPPHP_XMLObj":5:{s:4:"name";s:5:"entry";s:2:"ns";s:27:"http://www.w3.org/2005/Atom";s:5:"attrs";a:5:{s:5:"xmlns";s:27:"http://www.w3.org/2005/Atom";s:9:"xmlns:geo";s:28:"http://www.georss.org/georss";s:8:"xmlns:as";s:33:"http://activitystrea.ms/spec/1.0/";s:8:"xmlns:sf";s:37:"http://superfeedr.com/xmpp-pubsub-ext";s:8:"xml:lang";s:5:"en-us";}s:4:"subs";a:6:{i:0;O:13:"XMPPHP_XMLObj":5:{s:4:"name";s:2:"id";s:2:"ns";s:27:"http://www.w3.org/2005/Atom";s:5:"attrs";a:0:{}s:4:"subs";a:0:{}s:4:"data";s:76:"http://www.majorspoilers.com/review-star-wars-the-old-republic-the-lost-suns";}i:1;O:13:"XMPPHP_XMLObj":5:{s:4:"name";s:9:"published";s:2:"ns";s:27:"http://www.w3.org/2005/Atom";s:5:"attrs";a:0:{}s:4:"subs";a:0:{}s:4:"data";s:25:"2011-06-10T07:49:38+00:00";}i:2;O:13:"XMPPHP_XMLObj":5:{s:4:"name";s:7:"updated";s:2:"ns";s:27:"http://www.w3.org/2005/Atom";s:5:"attrs";a:0:{}s:4:"subs";a:0:{}s:4:"data";s:25:"2011-06-10T07:49:38+00:00";}i:3;O:13:"XMPPHP_XMLObj":5:{s:4:"name";s:5:"title";s:2:"ns";s:27:"http://www.w3.org/2005/Atom";s:5:"attrs";a:0:{}s:4:"subs";a:0:{}s:4:"data";s:76:"REVIEW: <b>Star Wars</b>: The Old Republic: The Lost Suns | Major <b>...</b>";}i:4;O:13:"XMPPHP_XMLObj":5:{s:4:"name";s:7:"summary";s:2:"ns";s:27:"http://www.w3.org/2005/Atom";s:5:"attrs";a:1:{s:4:"type";s:4:"html";}s:4:"subs";a:0:{}s:4:"data";s:207:"Based on a video game I&#39;ve never played and set alongside a video game that isn&#39;t out yet, <em>Star Wars</em>: The Old Republic: The Lost Sons is a half-hearted attempt at a <em>Star Wars</em> story.";}i:5;O:13:"XMPPHP_XMLObj":5:{s:4:"name";s:4:"link";s:2:"ns";s:27:"http://www.w3.org/2005/Atom";s:5:"attrs";a:4:{s:3:"rel";s:9:"alternate";s:4:"type";s:9:"text/html";s:4:"href";s:76:"http://www.majorspoilers.com/review-star-wars-the-old-republic-the-lost-suns";s:5:"title";s:76:"REVIEW: <b>Star Wars</b>: The Old Republic: The Lost Suns | Major <b>...</b>";}s:4:"subs";a:0:{}s:4:"data";s:0:"";}}s:4:"data";s:75:"
          
          
          
          
          
          
        ";}}s:4:"data";s:16:"
        
      ";}}s:4:"data";s:19:"
      
      
    ";}}s:4:"data";s:13:"
    
    
  ";}}s:4:"data";s:4:"
  
";}
SER;
        $xml = \unserialize($serialized);
        $raw = <<<RAW
<message xmlns='jabber:client' from='firehoser.superfeedr.com' to='hearsayer@superfeedr.com' >
    <event xmlns='http://jabber.org/protocol/pubsub#event' >
        <status xmlns='http://superfeedr.com/xmpp-pubsub-ext' feed='http://star-wars.alltop.com/rss' >
            <http xmlns='http://superfeedr.com/xmpp-pubsub-ext' code='200' >74972B in 1.117581297s, 2/100 new entries</http>
            <next_fetch xmlns='http://superfeedr.com/xmpp-pubsub-ext' >2011-06-10T07:53:29Z</next_fetch>
            <title xmlns='http://superfeedr.com/xmpp-pubsub-ext' >Alltop RSS</title>
            <subtitle xmlns='http://superfeedr.com/xmpp-pubsub-ext' >Alltop RSS feed for star-wars.alltop.com</subtitle>
            <entries_count_since_last_maintenance xmlns='http://superfeedr.com/xmpp-pubsub-ext' >53</entries_count_since_last_maintenance>
            <period xmlns='http://superfeedr.com/xmpp-pubsub-ext' >225</period>
            <last_fetch xmlns='http://superfeedr.com/xmpp-pubsub-ext' >2011-06-10T07:49:38Z</last_fetch>
            <last_parse xmlns='http://superfeedr.com/xmpp-pubsub-ext' >2011-06-10T07:49:38Z</last_parse>
            <last_maintenance_at xmlns='http://superfeedr.com/xmpp-pubsub-ext' >2011-06-09T22:31:47+00:00</last_maintenance_at>
            <link xmlns='http://superfeedr.com/xmpp-pubsub-ext' href='http://star-wars.alltop.com' title='' type='text/html' rel='alternate' ></link>
            <link xmlns='http://superfeedr.com/xmpp-pubsub-ext' href='http://star-wars.alltop.com/rss/' title='' type='application/rss+xml' rel='self' ></link>
    </status>
    <items xmlns='http://jabber.org/protocol/pubsub#event' node='http://star-wars.alltop.com/rss' >
        <item xmlns='http://jabber.org/protocol/pubsub' >
            <entry xmlns='http://www.w3.org/2005/Atom' xmlns:geo='http://www.georss.org/georss' xmlns:as='http://activitystrea.ms/spec/1.0/' xmlns:sf='http://superfeedr.com/xmpp-pubsub-ext' xml:lang='en-us' >
                <id xmlns='http://www.w3.org/2005/Atom' >http://news.google.com/news/url?sa=t&amp;fd=R&amp;usg=AFQjCNHDGvGv2tSTYEcYM21n2U2T6TFfKA&amp;url=http://www.denofgeek.com/television/933690/what_is_happening_with_star_wars_liveaction_series.html</id>
                <published xmlns='http://www.w3.org/2005/Atom' >2011-06-10T07:49:38+00:00</published>
                <updated xmlns='http://www.w3.org/2005/Atom' >2011-06-10T07:49:38+00:00</updated>
                <title xmlns='http://www.w3.org/2005/Atom' >What is happening with Star Wars live-action series? - Den Of Geek</title>
                <summary xmlns='http://www.w3.org/2005/Atom' type='html' >&lt;table border=&quot;0&quot; cellpadding=&quot;2&quot; cellspacing=&quot;7&quot;&gt;&lt;tr&gt;&lt;td width=&quot;80&quot; align=&quot;center&quot; valign=&quot;top&quot;&gt;&lt;/td&gt;&lt;td valign=&quot;top&quot;&gt;&lt;br /&gt;&lt;div&gt;&lt;img alt=&quot;&quot; height=&quot;1&quot; width=&quot;1&quot; /&gt;&lt;/div&gt;&lt;div&gt;&lt;a href=&quot;http://news.google.com/news/url?sa=t&amp;amp;fd=R&amp;amp;usg=AFQjCNHDGvGv2tSTYEcYM21n2U2T6TFfKA&amp;amp;url=http://www.denofgeek.com/television/933690/what_is_happening_with_star_wars_liveaction_series.html&quot;&gt;&lt;b&gt;What is happening with &lt;b&gt;Star Wars&lt;/b&gt; live-action series?&lt;/b&gt;&lt;/a&gt;&lt;br /&gt;&lt;b&gt;Den Of Geek&lt;/b&gt;&lt;br /&gt;First mentioned by George Lucas in the promotional tours for Revenge Of The Sith back in 2005, the &lt;b&gt;Star Wars&lt;/b&gt; live-action television series has sort of dipped off the radar since being trumpeted some years ago. Various snippets were released over the &lt;b&gt;...&lt;/b&gt;&lt;br /&gt;&lt;br /&gt;&lt;a href=&quot;http://news.google.com/news/more?pz=1&amp;amp;ned=us&amp;amp;ncl=dHN3ZeSthBtbokM&quot;&gt;&lt;nobr&gt;&lt;b&gt;&lt;/b&gt;&lt;/nobr&gt;&lt;/a&gt;&lt;/div&gt;&lt;/td&gt;&lt;/tr&gt;&lt;/table&gt;</summary><link xmlns='http://www.w3.org/2005/Atom' rel='alternate' type='text/html' href='http://news.google.com/news/url?sa=t&amp;fd=R&amp;usg=AFQjCNHDGvGv2tSTYEcYM21n2U2T6TFfKA&amp;url=http://www.denofgeek.com/television/933690/what_is_happening_with_star_wars_liveaction_series.html' title='What is happening with Star Wars live-action series? - Den Of Geek' ></link>
            </entry>
      </item>
      <item xmlns='http://jabber.org/protocol/pubsub' >
            <entry xmlns='http://www.w3.org/2005/Atom' xmlns:geo='http://www.georss.org/georss' xmlns:as='http://activitystrea.ms/spec/1.0/' xmlns:sf='http://superfeedr.com/xmpp-pubsub-ext' xml:lang='en-us' >
                <id xmlns='http://www.w3.org/2005/Atom' >http://www.majorspoilers.com/review-star-wars-the-old-republic-the-lost-suns</id>
                <published xmlns='http://www.w3.org/2005/Atom' >2011-06-10T07:49:38+00:00</published>
                <updated xmlns='http://www.w3.org/2005/Atom' >2011-06-10T07:49:38+00:00</updated>
                <title xmlns='http://www.w3.org/2005/Atom' >REVIEW: &lt;b&gt;Star Wars&lt;/b&gt;: The Old Republic: The Lost Suns | Major &lt;b&gt;...&lt;/b&gt;</title>
                <summary xmlns='http://www.w3.org/2005/Atom' type='html' >Based on a video game I&amp;#39;ve never played and set alongside a video game that isn&amp;#39;t out yet, &lt;em&gt;Star Wars&lt;/em&gt;: The Old Republic: The Lost Sons is a half-hearted attempt at a &lt;em&gt;Star Wars&lt;/em&gt; story.</summary>
                <link xmlns='http://www.w3.org/2005/Atom' rel='alternate' type='text/html' href='http://www.majorspoilers.com/review-star-wars-the-old-republic-the-lost-suns' title='REVIEW: &lt;b&gt;Star Wars&lt;/b&gt;: The Old Republic: The Lost Suns | Major &lt;b&gt;...&lt;/b&gt;' ></link>
            </entry>
      </item>
    </items>
  </event>
</message>
RAW;
        $payload = simplexml_load_string($raw)->event->asXml();

        // Sanity check for the payload
        $this->assertTrue(\strpos($payload, '<event ') === 0);

        $handler = $this->getMock('Hearsay\SuperfeedrBundle\Handler\HandlerInterface');

        $receiver = $this->getMockBuilder('Hearsay\SuperfeedrBundle\Xmpp\Superfeedr')
                ->disableOriginalConstructor()
                ->setMethods(array('subscribeTo'))
                ->getMock();
        $receiver->setHandler($handler);

        $received = null;
        $handler->expects($this->once())
                ->method('handleNotification')
                ->will($this->returnCallback(function($actual) use (&$received) {
                                    $received = $actual;
                                }));

        $receiver->handleMessage($xml);

        $this->assertXmlStringEqualsXmlString($received, $payload);
    }

}
