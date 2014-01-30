<?php

require_once 'chipin/short-url.php';

use \Chipin\ShortURL;

function testEncodingAndDecodingWidgetID() {
  foreach (array(1, 792, 9533, 54728921) as $id) {
    assertEqual($id, ShortURL\decodeID(ShortURL\encodeID($id)));
  }
}
