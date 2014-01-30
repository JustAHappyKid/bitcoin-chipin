<?php

namespace SpareParts\WebClient;

require_once 'spare-parts/web-client/exceptions.php'; # HostNameResolutionError
require_once 'spare-parts/http.php';                  # HTTP\Response
require_once 'spare-parts/url.php';                   # takeDomain, takePath

use \Exception, \SpareParts\URL, \SpareParts\HTTP, \SpareParts\WebClient\HostNameResolutionError;

class HttpClient {

  public function get($url) {
    $c = $this->getContent($url);
    if ($c instanceof HTTP\Response) {
      return $c;
    } else if (is_string($c)) {
      $r = new HTTP\Response;
      $r->url = $url;
      $r->statusCode = 200;
      $r->content = $c;
      return $r;
    } else {
      throw new Exception("Unexpected value/type returned for url $url");
    }
  }

  private function getContent($url) {
    if (beginsWith(URL\takeDomain($url), 'blockchain.info') &&
        beginsWith(URL\takePath($url), '/q/addressbalance/')) {
      return $this->addressBalanceRequest(withoutPrefix(URL\takePath($url), '/q/addressbalance/'));
    } else if ($url == 'http://blockchain.info/q/addressbalance/peanuts') {
      return 'Checksum does not validate';
    } else if (beginsWith($url, 'http://blockchain.info/tobtc?currency=USD')) {
      return '0.00778083';
    } else if ($url == 'http://api.easyjquery.com/ips/?ip=99.99.99.99') {
      return '{"Country": "US"}';
    } else if ($url == 'https://freegeoip.net/json/99.99.99.99') {
      return '{"ip":"99.99.99.99","country_code":"US","country_name":"United States",' .
        '"region_code":"","region_name":"","city":"","zipcode":"","latitude":38,' .
        '"longitude":-97,"metro_code":"","areacode":""}';
    } else if ($url == 'https://freegeoip.net/json/175.156.249.231') {
      return "404 page not found";
    } else {
      throw new Exception("Mock 'get' function couldn't handle following URL: $url");
    }
  }

  private function addressBalanceRequest($a) {
     if ($a == '1PUPt26votHesaGwSApYtGVTfpzvs8AxVM') {
      return '2537813';
    } else if ($a == '1E3FqrQTZSvTUdw7qZ4NnZppqiqnqqNcUN') {
      return '0';
    } else if ($a == '1K7dyLY6arFRXBidQhrtnyqksqJZdj2F37') {
      return '3600';
    } else if ($a == '15Mux55YKsWp9pe5eUC2jcP5R9K7XA4pPF') {
      return '0';
    } else if ($a == '1AkZUyVHtVsU6ZmAu1iSDhYiXbqFgKqzbt') {
      throw new HostNameResolutionError('Could not resolve hostname "blockchain.info"');
    } else if ($a == 'peanuts') {
      return 'Checksum does not validate';
    } else if ($a == '1TestForDowntimeX1iTDhViXbrogKqzbt') {
      return $this->errorResponse($this->blockchainDotInfoBeBackShortlyPage());
    } else if ($a == '1GivesBlankContentiTDhViXbrogKqzbt') {
      return $this->errorResponse('');
    } else if ($a == '1GivesLockWaitTimeoutTDhViXbrogKqzbt') {
      return $this->errorResponse('Lock wait timeout exceeded; try restarting transaction');
    }
  }

  private function errorResponse($content) {
    $r = new HTTP\Response;
    $r->statusCode = 500;
    $r->content = $content;
    return $r;
  }

  private function blockchainDotInfoBeBackShortlyPage() {
    return '
      <!DOCTYPE html><html>
        <head>
          <title>Site Under Maintenance - Blockchain.info</title>
        </head>
        <body>
          <div class="container" style="text-align:center">
            <h1 class="page-header">We Will Be Back Shortly</h1>
            <p class="well">
              Blockchain.info is currently down for maintenance. For status updates please see
              <a href="https://twitter.com/#!/blockchain">Twitter</a>. Apologies for any
              inconvenience.
            </p>
          </div>
        </body>
      </html>';
  }
}
