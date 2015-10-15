<?php
/**
 * @file
 * Stuff
 */

use ColumbusPHP\PokerHand\Feed\PokerHandFeedColumbusPHP;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

class PokerHandFeedColumbusPHPTest extends PHPUnit_Framework_TestCase {

  /**
   * Tests the get url method.
   */
  public function testGetUrl() {
    $feed = new PokerHandFeedColumbusPHP(new Client());
    $this->assertEquals('http://poker.columbusphp.org/hand', $feed->getUrl());
  }

  /**
   * Test a 200 response with JSON returned.
   */
  public function testgetData() {
    $raw_response = '[{"name":1,"hand":[{"card":"8C","suite":"Clubs","suite_abbr":"C","value":"Eight","value_abbr":8},{"card":"KS","suite":"Spades","suite_abbr":"S","value":"King","value_abbr":"K"},{"card":"QD","suite":"Diamonds","suite_abbr":"D","value":"Queen","value_abbr":"Q"},{"card":"JS","suite":"Spades","suite_abbr":"S","value":"Jack","value_abbr":"J"},{"card":"2S","suite":"Spades","suite_abbr":"S","value":"Two","value_abbr":2}]},{"name":2,"hand":[{"card":"3S","suite":"Spades","suite_abbr":"S","value":"Three","value_abbr":3},{"card":"4H","suite":"Hearts","suite_abbr":"H","value":"Four","value_abbr":4},{"card":"AS","suite":"Spades","suite_abbr":"S","value":"Ace","value_abbr":"A"},{"card":"4C","suite":"Clubs","suite_abbr":"C","value":"Four","value_abbr":4},{"card":"3H","suite":"Hearts","suite_abbr":"H","value":"Three","value_abbr":3}]}]';
    $expected_hands = [
      '1' => [
        ['suit' => 'C', 'card' => '8C', 'value' => 8],
        ['suit' => 'S', 'card' => 'KS', 'value' => 'K'],
        ['suit' => 'D', 'card' => 'QD', 'value' => 'Q'],
        ['suit' => 'S', 'card' => 'JS', 'value' => 'J'],
        ['suit' => 'S', 'card' => '2S', 'value' => 2],
      ],
      '2' => [
        ['suit' => 'S', 'card' => '3S', 'value' => 3],
        ['suit' => 'H', 'card' => '4H', 'value' => 4],
        ['suit' => 'S', 'card' => 'AS', 'value' => 'A'],
        ['suit' => 'C', 'card' => '4C', 'value' => 4],
        ['suit' => 'H', 'card' => '3H', 'value' => 3],
      ],
    ];

    $mock = new MockHandler([
      new Response(200, ['Content-Type' => 'application/json'], $raw_response),
    ]);
    $handler = HandlerStack::create($mock);
    $client = new Client(['handler' => $handler]);
    $feed = new PokerHandFeedColumbusPHP($client);

    // Get the raw data.
    $data = $feed->getData($feed->getUrl());
    $this->assertEquals($raw_response, $data);

    // Parse the data.
    $hands = $feed->parseData($data);
    $this->assertCount(2, $hands);
    $this->assertSame($expected_hands, $hands);
  }

  /**
   * Test a 200 response with HTML returned.
   *
   * @expectedException \Exception
   */
  public function testBadResponse() {
    $mock = new MockHandler([
      new Response(200, ['Content-Type' => 'text/html'], '<html><body></body></html>'),
    ]);
    $handler = HandlerStack::create($mock);
    $client = new Client(['handler' => $handler]);
    $feed = new PokerHandFeedColumbusPHP($client);

    // Get the raw data and try to parse it.
    $data = $feed->getData($feed->getUrl());
    $feed->parseData($data);
  }

  /**
   * Test a 404 response.
   *
   * @expectedException \GuzzleHttp\Exception\RequestException
   */
  public function test404Response() {
    $mock = new MockHandler([
      new Response(404),
    ]);
    $handler = HandlerStack::create($mock);
    $client = new Client(['handler' => $handler]);
    $feed = new PokerHandFeedColumbusPHP($client);

    // Try to get data.
    $data = $feed->getData($feed->getUrl());
  }
}
