<?php
/**
 * @file
 * PokerHandFeedRemote.php
 */

namespace ColumbusPHP\PokerHand\Feed;

use ColumbusPHP\PokerHand\Feed\PokerHandFeedInterface;
use GuzzleHttp\Exception\RequestException;

/**
 * Fetch the poker hands from a remote feed.
 */
class PokerHandFeedRemote implements PokerHandFeedInterface {

  /**
   * @var string
   */
  public $url = '';

  /**
   * @var \GuzzleHttp\ClientInterface
   */
  protected $client;

  /**
   * Create a new feed from ColumubsPHP feed.
   *
   * @param \GuzzleHttp\ClientInterface $client
   *   A guzzle client.
   */
  public function __construct(\GuzzleHttp\ClientInterface $client, $feed = 'http://poker.columbusphp.org/hand') {
    $this->url = $feed;
    $this->client = $client;
  }

  /**
   * {@inheritdoc }
   */
  public function getUrl() {
    return $this->url;
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
      $response = $this->client->get($url, $header_options);

      $result = $response->getBody()->getContents();
    }
    catch (RequestException $e) {
      throw $e;
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

      if ($info === NULL) {
        throw new \Exception();
      }

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
      throw $e;
    }

    return $normalized;
  }

}
