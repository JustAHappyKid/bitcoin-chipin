<?php

require_once dirname(__FILE__) . '/layout.php';

class PrivacyPolicyPage extends AboutPageLayout {

  function innerContent() { ?>
    <div id="about" class="about_page">
      <div class="container">
        <?= $this->pageHeader('Privacy Policy') ?>
        <div>

          <h3>Introduction</h3>
          <p>BitcoinChipin.com ("we" or "us") values its visitors' privacy. This
            privacy policy is effective May 26, 2013; it summarizes what information
            we might collect from a registered user or other visitor ("you"), and what
            we will and will not do with it. Please note that this privacy policy
            does not govern the collection and use of information by companies that
            BitcoinChipin.com does not control, nor by individuals not employed or
            managed by BitcoinChipin.com. If you visit a Web site that we mention or
            link to, be sure to review its privacy policy before providing the site
            with information.</p>

          <h3>What we do with your personally identifiable information</h3>
          <p>It is always up to you whether to disclose personally identifiable information to
            us, although if you elect not to do so, we reserve the right not to register you
            as a user or provide you with any products or services. "Personally identifiable
            information" means information that can be used to identify you as an individual,
            such as, for example:</p>
          <ul>
            <li>your name, company, email address, phone number, billing address, and
              shipping address</li>
            <li>your BitcoinChipin.com user ID and password</li>
            <li>any account-preference information you provide us</li>
            <li>your computer's domain name and IP address, indicating where your computer is
              located on the Internet</li>
            <li>session data for your login session, so that our computer can 'talk' to yours
              while you are logged in</li>
          </ul>

          <p>If you do provide personally identifiable information to us, either directly or
            through a reseller or other business partner, we will:</p>
          <ul>
            <li>not sell or rent it to a third party without your permission - although unless you opt out (see below), we may use your contact information to provide you with information we believe you need to know or may find useful, such as (for example) news about our services and products and modifications to the Terms of Service;</li>
            <li>take commercially reasonable precautions to protect the information from loss, misuse and unauthorized access, disclosure, alteration and destruction;
            not use or disclose the information except:</li>
              <ul>
                <li>as necessary to provide services or products you have ordered, such as (for example) by providing it to a carrier to deliver products you have ordered;
                in other ways described in this privacy policy or to which you have otherwise consented;</li>
                <li>in the aggregate with other information in such a way so that your identity cannot reasonably be determined (for example, statistical compilations);
                as required by law, for example, in response to a subpoena or search warrant;</li>
                <li>to outside auditors who have agreed to keep the information confidential;</li>
                <li>as necessary to enforce the Terms of Service;</li>
                <li>as necessary to protect the rights, safety, or property of BitcoinChipin.com, its users, or others; this may include (for example) exchanging information with other organizations for fraud protection and/or risk reduction.</li>
              </ul>
          </ul>

          <h3>Other information we collect</h3>
          <p>We may collect other information that cannot be readily used to identify you,
            such as (for example) the domain name and IP address of your computer. We may use
            this information, individually or in the aggregate, for technical administration
            of our Web site(s); research and development; customer- and account administration;
            and to help us focus our marketing efforts more precisely.</p>

          <h3>Cookies</h3>
          <p>BitcoinChipin.com uses "cookies" to store personal data on your computer. We may also link information stored on your computer in cookies with personal data about specific individuals stored on our servers. If you set up your Web browser (for example, Chrome, Internet Explorer, or Firefox) so that cookies are not allowed, you might not be able to use some or all of the features of our Web site(s).</p>

          <h3>External data storage sites</h3>
          <p>We may store your data on servers provided by third party hosting vendors with
            whom we have contracted.</p>

          <h3>Your privacy responsibilities</h3>
          <p>To help protect your privacy, be sure:</p>
          <ul>
            <li>not to share your user ID or password with anyone else;</li>
            <li>to log off the BitcoinChipin.com Web site when you are finished;</li>
            <li>to take customary precautions to guard against "malware" (viruses, Trojan horses, bots, etc.), for example by installing and updating suitable anti-virus software.</li>
          </ul>

          <h3>Notice to European Union users</h3>
          <p>BitcoinChipin.com‘s operations are located primarily in the United States. If you provide information to us, the information will be transferred out of the European Union (EU) to the United States. By providing personal information to us, you are consenting to its storage and use as described herein.</p>

          <h3>Information collected by children</h3>
          <p>You must be at least 13 years old to use BitcoinChipin.com's Web site(s) and service(s). BitcoinChipin.com does not knowingly collect information from children under 13. (See the  Children's Online Privacy Protection Act.)</p>

          <h3>Changes to this privacy policy</h3>
          <p>We reserve the right to change this privacy policy as we deem necessary or appropriate because of legal compliance requirements or changes in our business practices. If you have provided us with an email address, we will endeavor to notify you, by email to that address, of any material change to how we will use personally identifiable information.</p>

          <h3>Questions or comments?</h3>
          <p>If you have questions or comments about BitcoinChipin.com's privacy policy, send
            an email to <a href="mailto:support@bitcoinchipin.com">support@bitcoinchipin.com</a>,
            or contact us via any of the ways described in the
            <a href="/about/">About Us page</a>.</p>

          <p>Thank you for choosing BitcoinChipin.com!</p>
        </div>
      </div>
    </div>
  <? }
}