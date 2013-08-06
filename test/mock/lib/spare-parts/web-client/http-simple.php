<?php

namespace SpareParts\WebClient\HttpSimple;

use \Exception;

function get($url) {
  if ($url == 'http://blockchain.info/q/addressbalance/1PUPt26votHesaGwSApYtGVTfpzvs8AxVM') {
    return '2537813';
  } else if (beginsWith($url, 'http://blockchain.info/tobtc?currency=USD')) {
    return '0.00778083';
  } else if ($url == 'http://api.easyjquery.com/ips/?ip=99.99.99.99') {
    return '{"Country": "US"}';
  } else if ($url == 'https://freegeoip.net/json/99.99.99.99') {
    return '{"ip":"99.99.99.99","country_code":"US","country_name":"United States",' .
      '"region_code":"","region_name":"","city":"","zipcode":"","latitude":38,' .
      '"longitude":-97,"metro_code":"","areacode":""}';
  } else {
    throw new Exception("Mock 'get' function couldn't handle following URL: $url");
  }
}
