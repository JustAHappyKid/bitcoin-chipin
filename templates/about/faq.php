<?php

require_once dirname(dirname(__FILE__)) . '/layout.php';

class FaqPage extends Layout {

  function userIsAuthenticated() { return false; }

  function htmlHeadExtras() { ?>
    <link rel="stylesheet" type="text/css" href="/measure-theme/css/external-pages.css" />
  <? }

  function innerContent() { ?>
    <div id="faq" class="faq_page">
      <div class="container">
        <h2 class="section_header">
          <hr class="left visible-desktop" />
          <span>FAQ</span>
          <hr class="right visible-desktop" />
        </h2>
        <div class="row">
          <div class="span12">
            <div class="faq">
              <div class="number">1</div>
              <div class="question">
                Are my bitcoins safe with BitcoinChipin.com?
              </div>
              <div class="answer">
                Yes. We never have access to any of your bitcoins. They are sent directly
                to a Bitcoin address that you own, and BitcoinChipin.com simply monitors
                the Bitcoin network to display progress for your chip-in widget in real time.
              </div>
            </div>
            <div class="faq">
              <div class="number">2</div>
              <div class="question">
                What does this service cost?
              </div>
              <div class="answer">
                BitcoinChipin.com is free to use.
              </div>
            </div>
            <div class="faq">
              <div class="number">3</div>
              <div class="question">
                What can I use BitcoinChipin.com for?
              </div>
              <div class="answer">
                Since we never hold your bitcoins, we don't have any restrictions on what
                you may use it for. We will soon be introducing a Widget Gallery and social-media
                sharing to make it easier for you to promote your campaign.
              </div>
            </div>
            <div class="faq">
              <div class="number">4</div>
              <div class="question">
                Is there any restriction on the number of widgets I can create?
              </div>
              <div class="answer">
                No, you may create as many widgets as you like.
              </div>
            </div>
            <div class="faq">
              <div class="number">5</div>
              <div class="question">
                Where can I purchase bitcoins?
              </div>
              <div class="answer">
                We recommend using <a href="https://bitinstant.com/">BitInstant</a>.
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  <? }
}
