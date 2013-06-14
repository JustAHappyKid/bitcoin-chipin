<?php

require_once dirname(__FILE__) . '/layout.php';

class ResourcesPage extends AboutPageLayout {

  function innerContent() { ?>
    <div id="features" class="features_page">
      <div class="container">
        <div class="features2">
          <h2 class="section_header">
            <hr class="left visible-desktop">
            <span>Bitcoin Resources</span>
            <hr class="right visible-desktop">
          </h2>
          <div class="row">
            <div class="span12">
              <h3 class="intro">
                The Bitcoin ecosystem is growing rapidly. Below are some helpful site to help
                you learn all the basics about the currency and be able to start funding your
                project today!
              </h3>
            </div>
          </div>
          <div class="row">
            <div class="span4 feature2">
              <div class="icon"><i class="cloud"></i></div>
              <h4>Many desktop publishing editor these available</h4>
              <p>You can't do better design with a computer, but you can speed up your work
                enormously.</p>
            </div>
            <div class="span4 feature2">
              <div class="icon"><i class="tools"></i></div>
              <h4>Many desktop publishing editor these available</h4>
              <p>You can't do better design with a computer, but you can speed up your work
                enormously.</p>
            </div>
            <div class="span4 feature2">
              <div class="icon"><i class="graph"></i></div>
              <h4>Many desktop publishing editor these available</h4>
              <p>You can't do better design with a computer, but you can speed up your work
                enormously.</p>
            </div>
          </div>
          <div class="row">
            <div class="span4 feature2">
              <div class="icon"><i class="mobile"></i></div>
              <h4>Many desktop publishing editor these available</h4>
              <p>You can't do better design with a computer, but you can speed up your work
                enormously.</p>
            </div>
            <div class="span4 feature2">
              <div class="icon"><i class="lab"></i></div>
              <h4>Many desktop publishing editor these available</h4>
              <p>You can't do better design with a computer, but you can speed up your work
                enormously.</p>
            </div>
            <div class="span4 feature2">
              <div class="icon"><i class="secure"></i></div>
              <h4>Many desktop publishing editor these available</h4>
              <p>You can't do better design with a computer, but you can speed up your work
                enormously.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div id="features" class="features_page">
      <div class="container">
        <div class="features1">
          <h2 class="section_header">
            <hr class="left visible-desktop">
            <span>Our Partners</span>
            <hr class="right visible-desktop">
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
              <div class="span4 feature">
                <a href="https://www.bitinstant.com/"
                   ><img src="/img/partners/bitinstant.jpg" alt="" class="thumb" /></a>
                <h3><i></i> BitInstant</h3>
                <p class="description">
                  BitInstant is a payment processor for various Bitcoin exchanges and other
                  merchants. Customers of these exchanges and other merchants can easily make
                  a payment to them by using BitInstant, that is an agent and payment processor
                  for those exchanges and other merchants.
                </p>
              </div>
              <div class="span4 feature">
                <a href="http://memorydealers.com/"
                   ><img src="/img/partners/memorydealers.jpg" alt="" class="thumb" /></a>
                <h3><i></i> MemoryDealers</h3>
                <p class="description">
                  Memory Dealers offers the largest selection of discounted and custom
                  transceivers, memory, and hardware. We design, manufacture, and market memory
                  modules for use in Cisco, Sun, Juniper, PCs, laptops, and legacy devices.
                </p>
              </div>
              <div class="span4 feature">
                <a href="http://www.weusecoins.com/"
                   ><img src="/img/partners/weusecoins.jpg" alt="" class="thumb" /></a>
                <h3><i></i> WeUseCoins</a></h3>
                <p class="description">
                  WeUseCoins.com is a website dedicated to making Bitcoin more accessible to
                  beginners. It's currently managed by Bitcoin developer Stefan Thomas with
                  the help of many other individuals from the Bitcoin community.
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  <? }
}
