<?php
/**
 * @file
 * run.php
 *
 * PHP Code Jam challange.
 */

$loader = require 'vendor/autoload.php';
$loader->register();

use ColumbusPHP\PokerHand\Feed\PokerHandFeedColumbusPHP;
use ColumbusPHP\PokerHand\Collection\PokerHandCollection;
use GuzzleHttp\Client;

try {
  // Get a poker hand collection and rank 'em.
  $client = new Client();
  $hands = PokerHandCollection::createFromFeed(new PokerHandFeedColumbusPHP($client));

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
