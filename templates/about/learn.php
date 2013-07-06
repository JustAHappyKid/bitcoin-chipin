<?php

require_once dirname(__FILE__) . '/layout.php';

class LearnPage extends AboutPageLayout {

  function innerContent() { ?>
    <div id="features" class="features_page">
      <div class="container">
        <div class="features2">
          <h2 class="section_header">
            <hr class="left visible-desktop" />
            <span>Bitcoin Resources</span>
            <hr class="right visible-desktop" />
          </h2>
          <div class="row">
            <div class="span12">
              <h3 class="intro">
                The Bitcoin ecosystem is growing rapidly. Below are some helpful sites to help
                you learn all the basics and get you on your way to funding your
                own project today!
              </h3>
            </div>
          </div>
          <div class="row">
            <div class="offset2 span4 feature2">
              <div class="icon"><i class="graph"></i></div>
              <h4><a href="http://www.weusecoins.com/">WeUseCoins.com</a></h4>
              <p>&ldquo;Our mission is to support Bitcoin by making it easier
                for new users to get started.&rdquo;</p>
            </div>
            <div class="span4 feature2">
              <div class="icon"><i class="tools"></i></div>
              <h4><a href="http://bitcoin.cbtnuggets.com/"
                     >Bitcoin Training Series from CBT Nuggets</a></h4>
              <p>&ldquo;Learn what it is, how it works, and how you can get started
                using it in this groundbreaking CBT Nuggets series with trainer
                Keith Barker.&rdquo;</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  <? }
}
