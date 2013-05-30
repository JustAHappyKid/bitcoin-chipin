<?php

namespace SpareParts\WebClient\HttpSimple;

function get($url) {
  if ($url == 'http://blockchain.info/q/addressbalance/1PUPt26votHesaGwSApYtGVTfpzvs8AxVM') {
    return '2537813';
  } else if ($url == 'http://blockchain.info/tobtc?currency=USD&value=1') {
    return '0.00778083';
  } else if ($url == 'http://api.easyjquery.com/ips/?ip=99.99.99.99') {
    return '{"Country": "CH"}';
  } else {
    throw new Exception("Mock 'get' function couldn't handle following URL: $url");
  }
}
