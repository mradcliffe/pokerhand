<?php
/**
 * @file
 * run.php
 *
 * PHP Code Jam challange.
 */

$loader = require 'vendor/autoload.php';
$loader->register();

use PokerHand\PokerHand;
use PokerHand\Feed\PokerHandFeedColumbusPHP;
use PokerHand\Collection\PokerHandCollection;

// Get a poker hand collection and rank 'em.
$hands = PokerHandCollection::createFromFeed(new PokerHandFeedColumbusPHP);

$hands->rankHands()->sortHands();

$winner = TRUE;
foreach ($hands->hands as $player => $hand) {
  if ($winner) {
    print 'Winner: ' . $player . "\n\n";
  }

  print "\t$player: " . $hand->__toString() . "\t(" . $hand::$ranks[$hand->hand_rank] . ")\n";
  $winner = FALSE;
}
