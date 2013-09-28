<?php
/**
 * @file
 * PokerHandFeedInterface.php
 */

namespace ColumbusPHP\PokerHand\Feed;

/**
 * Feed Interface for poker.columbusphp.org
 */
interface PokerHandFeedInterface {

  /**
   * Provide the URL for the feed.
   *
   * @return string
   *   URL.
   */
  public function getUrl();

  /**
   * Parse the output from the feed.
   *
   * @param $data
   *   The data from the feed.
   * @return array
   *   An indexed array of poker hands where each poker hand is an associative
   *   array with the following keys:
   *     - suit: The suit abbrevation.
   *     - value: The card value i.e. 2, A, K, 10, etc...
   *     - card: The card and  suit abbreviation i.e. KH, 10S, 5C, AD, etc...
   */
  public function parseData($data);

  /**
   * Get data
   *
   * @param $url
   *   The URL to get the data from.
   * @return string
   *   An array of data.
   */
  public function getData($url);

}
