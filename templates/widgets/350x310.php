<!DOCTYPE html>
<html>
  <head>
    <title>Bitcoins</title>
    <link rel="stylesheet" href="/widgets/css/styles.css" />
  </head>
  <body>
    <div class="widget-wrapper">
      <div class="widget widget-white">

        <h2 class="widget-title">$title</h2>

        <div id="overview" style="<?php if ($display != 'overview') { ?>display: none;<? } ?>">
          <div class="about">
            <p>
              ? $summary = substr($about, 0, 110);
              <?= $summary ?>
              ? if ($summary != $about) {
                &hellip;
                <!--<span>[<a href="xxx">more</a>]</span>-->
              }
            </p>
          </div>
          <div class="raised-and-goal">
            <div class="raised">
              <h6>raised so far</h6>
              <h2>$raised<!-- <span class="currency">$currency</span>--></h2>
              <h6>$altRaised <!--$altCurrency--></h6>
            </div>
            <div class="goal">
              <h6>the goal</h6>
              <h2>$goal<!-- <span class="currency">$currency</span>--></h2>
              <h6>$altGoal<!-- $altCurrency--></h6>
            </div>
          </div>
          <div class="status-bar-container">
            <div class="bg-bar">
              ? $widthCSS = "width: " . $progress . "%;";
              <div class="bar" style="$widthCSS"></div>
            </div>
          </div>
          <div class="btn-container">
            <a href="?display=contribute" class="btn"
               onclick="document.getElementById('overview').setAttribute('style', 'display: none;');
                        document.getElementById('contribute').setAttribute('style', '');
                        return false;">Contribute Now</a>
          </div>
        </div>

        <div id="contribute" style="<?php if ($display != 'contribute') { ?>display: none;<? } ?>">
          <h6>Send your contribution to:</h6>
          <div class="btc-addr">$bitcoinAddress</div>
          <div class="qr-code">
            <img src="http://blockchain.info/qr?data=$bitcoinAddress&amp;size=100"
                 alt="[QR Code]" style="width: 100px; height: 100px;" />
          </div>
          <!--<div><a class="buy-bitcoins" href="">Learn How to Buy Bitcoins</a></div>-->
          <div class="btn-container">
            <a href="?display=overview" class="btn"
               onclick="document.getElementById('contribute').setAttribute('style', 'display: none;');
                        document.getElementById('overview').setAttribute('style', '');
                        return false;">View Progress</a>
          </div>
        </div>

        <div id="footer">
          <!--<a class="buy_bitcoins" href="">Learn How to Buy Bitcoins</a>
          <br />-->
          <a href="" class="powered">powered by bitcoinchipin.com</a>
        </div>
      </div>
    </div>
  </body>
</html>
