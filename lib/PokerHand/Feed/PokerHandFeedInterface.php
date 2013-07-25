<?php
/**
 * @file
 * PokerHandFeedInterface.php
 */

namespace PokerHand\Feed;

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
   *   An array of hands.
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
