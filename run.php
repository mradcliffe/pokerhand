<?php
/**
 * @file
 * run.php
 *
 * PHP Code Jam challange.
 */

$loader = require 'vendor/autoload.php';
$loader->register();

use ColumbusPHP\PokerHand\Feed\PokerHandFeedDummy;
use ColumbusPHP\PokerHand\Feed\PokerHandFeedRemote;
use ColumbusPHP\PokerHand\Collection\PokerHandCollection;
use GuzzleHttp\Client;

try {
  // Get a poker hand collection and rank 'em.
  $client = new Client();

  // Check if there is a URL parameter and use the appropriate feed.
  if (!isset($argv[1]) || filter_var($argv[1], FILTER_VALIDATE_URL) === FALSE) {
    print "\nUsing dummy feed:\n\n";
    $hands = PokerHandCollection::createFromFeed(new PokerHandFeedDummy($client));
  } else {
    print "\nUsing remote feed:\n\n";
    $hands = PokerHandCollection::createFromFeed(new PokerHandFeedRemote($client, $argv[1]));
  }

  $hands->rankHands()->sortHands();

  $winner = TRUE;
  foreach ($hands->hands as $player => $hand) {
    if ($winner) {
      print 'Winner: ' . $player . "\n\n";
    }

    print "\t$player: " . $hand->__toString() . "\t(" . $hand::$ranks[$hand->hand_rank] . ")\n";
    $winner = FALSE;
  }
}
catch (\Exception $e) {
  print "An error occurred trying to run this program: " . $e->getMessage() . "\n\n";
}
