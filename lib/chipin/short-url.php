<?php

namespace Chipin\ShortURL;

require_once 'chipin/widgets.php';
use \Chipin\Widgets\Widget;

function encodeID($id) { return dechex(0xfff + $id); }
function decodeID($id) { return hexdec($id) - 0xfff; }

function urlForWidget(Widget $w) {
  return "http://btcchip.in/" . encodeID($w->id);
}
