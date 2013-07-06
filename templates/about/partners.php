<?php

require_once dirname(__FILE__) . '/layout.php';

class PartnersPage extends AboutPageLayout {

  function innerContent() { ?>
    <div id="features" class="features_page">
      <div class="container">
        <div class="features1">
          <h2 class="section_header">
            <hr class="left visible-desktop" />
            <span>Our Partners</span>
            <hr class="right visible-desktop" />
          </h2>
          <div class="row">
            <div class="span12">
              <h3 class="intro">
                We're proud to be working with the following organizations who are
                working hard to support the Bitcoin ecosystem.
              </h3>
            </div>
          </div>
          <div id="container">
            <div class="row">
              <div class="span4 feature partner">
                <a href="https://www.bitinstant.com/"
                   ><img src="/img/partners/bitinstant.jpg" alt="" class="thumb" /></a>
                <h3>BitInstant</h3>
                <p class="description">
                  BitInstant is a payment processor for various Bitcoin exchanges and other
                  merchants. Customers of these exchanges and other merchants can easily make
                  a payment to them by using BitInstant, that is an agent and payment processor
                  for those exchanges and other merchants.
                </p>
              </div>
              <div class="span4 feature partner">
                <a href="http://memorydealers.com/"
                   ><img src="/img/partners/memorydealers.jpg" alt="" class="thumb" /></a>
                <h3>MemoryDealers</h3>
                <p class="description">
                  Memory Dealers offers the largest selection of discounted and custom
                  transceivers, memory, and hardware. We design, manufacture, and market memory
                  modules for use in Cisco, Sun, Juniper, PCs, laptops, and legacy devices.
                </p>
              </div>
              <div class="span4 feature partner">
                <a href="http://www.weusecoins.com/"
                   ><img src="/img/partners/weusecoins.jpg" alt="" class="thumb" /></a>
                <h3>WeUseCoins</a></h3>
                <p class="description">
                  WeUseCoins.com is a website dedicated to making Bitcoin more accessible to
                  beginners. It's currently managed by Bitcoin developer Stefan Thomas with
                  the help of many other individuals from the Bitcoin community.
                </p>
              </div>
            </div>
            <div class="row">
              <div class="span4 feature partner">
                <a href="https://www.bitcoinstore.com/"
                   ><img src="/img/partners/bitcoinstore.png" alt="" class="thumb" /></a>
                <h3>BitcoinStore.com</h3>
                <p class="description">
                  BitcoinStore.com is the largest purveyor of electronics accepting Bitcoin as
                  a payment method. Choose from thousands of computers, telecommunications
                  devices, office supplies, and much more, all at competitive prices.
                </p>
              </div>
              <div class="span4 feature partner">
                <a href="https://www.bitpay.com/"
                   ><img src="/img/partners/bitpay.png" alt="" class="thumb" /></a>
                <h3>BitPay</h3>
                <p class="description">
                  BitPay is the world's largest Bitcoin payment processor.
                  They are trusted by thousands of merchants to accept bitcoins,
                  as a form of payment,
                  just as they might accept payments from Visa, Mastercard, or Paypal.
                </p>
              </div>
              <div class="span4 feature partner">
                <a href="https://blockchain.info/"
                   ><img src="/img/partners/blockchain.png" alt="" class="thumb" /></a>
                <h3>Blockchain.info</h3>
                <p class="description">
                  Blockchain.info is the most popular web-based Bitcoin wallet and block explorer.
                  As of January, 2013 the site has over 110,000 registered users.
                </p>
              </div>
            </div>
            <div class="row">
              <div class="offset4 span4 feature partner">
                <a href="https://coinapult.com/"
                   ><img src="/img/partners/coinapult.png" alt="" class="thumb" /></a>
                <h3>Coinapult</h3>
                <p class="description">
                  Coinapult allows you to send bitcoins to any email address or
                  mobile phone (via SMS). The service is completely free, and thus an excellent
                  way to introduce new people to the world of Bitcoin.
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  <? }
}
