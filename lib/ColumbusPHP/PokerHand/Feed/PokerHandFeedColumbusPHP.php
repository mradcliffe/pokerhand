<?php
/**
 * @file
 * PokerHandFeedColumbusPHP.php
 */

namespace ColumbusPHP\PokerHand\Feed;

use ColumbusPHP\PokerHand\Feed\PokerHandFeedInterface;

/**
 * Fetch the poker hands from Bill Condo's feed.
 */
class PokerHandFeedColumbusPHP implements PokerHandFeedInterface {

  /**
   * {@inheritdoc }
   */
  static public $url = 'http://poker.columbusphp.org/hand';

  /**
   * {@inheritdoc }
   */
  public function getUrl() {
    return self::$url;
  }

  /**
   * {@inheritdoc }
   */
  public function getData($url) {
    $result = '';
    $header_options = array(
      'Content-Type: application/json',
      'Accept: application/json',
    );

    try {
      $ch = curl_init();

      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $header_options);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
      curl_setopt($ch, CURLOPT_USERAGENT, 'PokerHandFeedColumbusPHP');

      $result = curl_exec($ch);
      $result_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

      if (!in_array($result_code, array(200))) {
        throw new \Exception('Error: ' . curl_errno($ch) . ' ' . curl_error($ch));
      }
    }
    catch (\Exception $e) {
      print 'An error occurred trying to get the feed.';
    }

    return $result;
  }

  /**
   * {@inheritdoc }
   */
  public function parseData($data) {
    $info = array();
    $normalized = array();

    try {
      $info = json_decode($data);

      // Normalize the hand object into an array.
      foreach ($info as $index => $hand) {
        $normalized[$hand->name] = array();

        // Normalize each card object into an array. "T" is a dumb abbreviation
        // and I'm turning it back to 10. No, it's not really dumb, Bill. It
        // makes sense for fixed width blah blah blah I'm not listening.
        foreach ($hand->hand as $card_index => $card) {
          $normalized[$hand->name][$card_index] = array(
            'suit' => $card->suite_abbr,
            'card' => str_replace('T', 10, $card->card),
            'value' => ($card->value_abbr == 'T') ? 10 : $card->value_abbr,
          );
        }
      }
    }
    catch (\Exception $e) {
      print 'An error occurred trying to parse the feed data.';
    }

    return $normalized;
  }

}
