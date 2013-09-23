<?php

require_once dirname(__FILE__) . '/layout.php';

class FaqPage extends AboutPageLayout {

  function innerContent() { ?>
    <div id="faq" class="faq_page">
      <div class="container">
        <?= $this->pageHeader('FAQ') ?>
        <div style="font-size: 15px; margin-bottom: 25px;">
          <p>BitcoinChipin.com is a free service to help you raise money for your cause
            using Bitcoin. Learn more below.</p>
        </div>
        <div class="row">
          <div class="span12">
            <div class="faq">
              <div class="number">1</div>
              <div class="question">
                What is Bitcoin?
              </div>
              <div class="answer">
                Bitcoin is a revolutionary new kind of money that makes it fast and
                inexpensive (usually free) to send value to anyone across the globe.
                To learn more about Bitcoin and how it can benefit you, we recommend
                <a href="http://bitcoin.org">starting with the video here</a>.
              </div>
            </div>
            <div class="faq">
              <div class="number">2</div>
              <div class="question">
                Where can I purchase bitcoins?
              </div>
              <div class="answer">
                We recommend using
                <a href="https://www.bitstamp.net/" target="_blank">Bitstamp</a>.
              </div>
            </div>
            <div class="faq">
              <div class="number">3</div>
              <div class="question">
                What can I use BitcoinChipin.com for?
              </div>
              <div class="answer">
                Just about anything &mdash; raise money to help keep your website running, for
                charity, or just because you want to give it a try!
                Since we never hold your bitcoins, we don't have any restrictions on what
                you may use it for. We will soon be introducing a Widget Gallery and social-media
                sharing to make it easier for you to promote your campaign.
              </div>
            </div>
            <div class="faq">
              <div class="number">4</div>
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
              <div class="number">5</div>
              <div class="question">
                What does this service cost?
              </div>
              <div class="answer">
                BitcoinChipin.com is free to use.
              </div>
            </div>
            <div class="faq">
              <div class="number">6</div>
              <div class="question">
                Is there any restriction on the number of widgets I can create?
              </div>
              <div class="answer">
                No, you may create as many widgets as you like.
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  <? }
}
