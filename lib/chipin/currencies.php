<?php

namespace Chipin\Currencies;

function BTC() { return 'BTC'; }
function CAD() { return 'CAD'; }
function CNY() { return 'CNY'; }
function EUR() { return 'EUR'; }
function GBP() { return 'GBP'; }
function JPY() { return 'JPY'; }
function USD() { return 'USD'; }

function codes() { return array(USD(), EUR(), GBP(), CNY(), CAD(), JPY(), BTC()); }
