<?php

require_once dirname(__FILE__) . '/layout.php';

class AboutUsPage extends AboutPageLayout {

  function innerContent() { ?>
    <div id="about" class="about_page">
      <div class="container">
        <h2 class="section_header">
          <hr class="left visible-desktop" />
          <span>About Us</span>
          <hr class="right visible-desktop" />
        </h2>
        <div class="row">
          <div class="offset2 span8">
            <p>Bitcoinchipin.com is brought to you courtesy of
              <a href="http://www.memorydealers.com">MemoryDealers.com</a>.</p>
            <p><a href="http://www.memorydealers.com">MemoryDealers</a> has been in business
              over 10 years selling optical transceivers, memory and other Cisco equipment to
              customers all over the world.  MemoryDealers is a core supporter of Bitcoin and
              has been responsible for such things as lovebitcoins.org,
              <a href="http://www.bitcoinblogger.com/2011/06/bitcoin-billboard-in-silicon-valley.html"
                 >the Bitcoin Billboard</a>,
              and the $10,000 Bitcoin bet.</p>
            <div style="margin: 25px 0; text-align: center;">
              <iframe width="420" height="315" src="https://www.youtube.com/embed/gfydIbhduu0"
                      frameborder="0" allowfullscreen
                      style="border: 5px solid #dde;"></iframe>
            </div>
            <p>To support our efforts, please buy your networking equipment from
              <a href="http://www.memorydealers.com">MemoryDealers.com</a>.</p>
          </div>
        </div>
      </div>
    </div>
  <? }
}
