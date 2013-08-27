<?php
/**
 * @file
 * run.php.
 */

$loader = require 'vendor/autoload.php';
$loader->register();

use PokerHand\PokerHand;
use PokerHand\Feed\PokerHandFeedGenerator;
use PokerHand\Collection\PokerHandCollection;

$game = PokerHandCollection::createFromFeed(PokerHandFeedGenerator::createGameOf(10));

$game->rankHands()->sortHands();

$winner = TRUE;
foreach ($game->hands as $player => $hand) {
  if ($winner) {
    print 'Winner: ' . $player . "\n\n";
  }

  print "\t$player: " . $hand->__toString() . "\t(" . $hand::$ranks[$hand->hand_rank] . ")\n";
  $winner = FALSE;
}
