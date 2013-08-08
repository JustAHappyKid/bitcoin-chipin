
  <!--suppress HtmlUnknownTarget -->
  <div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
      <div class="container">
        <!-- NOTE: This bit is necessary for the "drop-down" version of the nav-menu, as
             seen on smaller displays/screens/devices. -->
        <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </a>
        <a class="brand" href="/">
          <img src="/images/logo.gif" width="50" height="40" alt=""
               style="opacity: 0.5; float: left;" />
          <div style="float: left; margin-left: 3px; margin-top: 23px;
                      color: #f66; font-size: 15px; font-weight: bold;
                      opacity: 0.8;">
            BETA</div>
        </a>
        <div class="nav-collapse collapse">
          <ul class="nav pull-left">
            <li><a href="/about/">About</a></li>
            <li><a href="#footer" class="scroller" data-section="#footer">Contact Us</a></li>
            <li><a href="/about/faq">FAQ</a></li>
          </ul>
          <ul class="nav pull-right">
            <? if ($this->user): ?>
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                  Your Account <b class="caret"></b>
                </a>
                <ul class="dropdown-menu">
                  <li><a href="/dashboard/">Your Widgets</a></li>
                  <li><a href="/account/change-password">Change Password</a></li>
                  <li><a href="/account/signout">Sign Out</a></li>
                </ul>
            </li>
            <? else: ?>
              <li><a class="btn-header" href="/account/signup">Sign Up</a></li>
              <li><a class="btn-header" href="/account/signin">Sign In</a></li>
            <? endif; ?>
          </ul>
        </div>
      </div>
    </div>
  </div>

