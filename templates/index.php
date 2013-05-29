<?php

require_once dirname(__FILE__) . '/layout.php';

class IndexPage extends Layout {

  function userIsAuthenticated() { return false; }

  function innerContent() { ?>
    <div id="hero">
      <div class="container">
        <!-- starts carousel -->
        <div class="row animated fadeInDown">
          <div class="span12">
            <div id="myCarousel" class="carousel slide">
              <div class="carousel-inner">
                <!-- slide 1 -->
                <div class="active item slide1">
                  <div class="row">
                    <div class="span6"><img src="/img/homepage/slide1.png" alt="" /></div>
                    <div class="span4">
                      <h1>Raise money with bitcoins</h1>
                      <p>It's easy to start a fundraising campaign using our widget creator.
                        Within three steps you'll be on your way to stacking chips!</p>
                      <a href="/account/signup" class="btn btn-warning btn-large">Sign Up!</a>
                    </div>
                  </div>
                </div>
                <!-- slide -->
                <div class="item slide2">
                  <div class="row">
                    <div class="span4 animated fadeInUpBig">
                      <h1>New to Bitcoin?</h1>
                      <p>The world of Bitcoin is rapidly growing. If you're new to
                        the revolution we've got some great resources to bring you up
                        to speed.</p>
                      <a href="blog.html" class="btn btn-warning btn-large">Start Learning!</a>
                    </div>
                    <div class="span6 animated fadeInDownBig">
                      <img src="/img/homepage/slide2.png" alt="" />
                    </div>
                  </div>
                </div>
              </div> <!-- /.carousel-inner -->
              <!-- Carousel navigation -->
              <a class="carousel-control left" href="#myCarousel" data-slide="prev">&lsaquo;</a>
              <a class="carousel-control right" href="#myCarousel" data-slide="next">&rsaquo;</a>
            </div>
          </div>
        </div>
      </div> <!-- /.container -->
    </div>
    <div id="intro">
      <div class="container">
        <h1>Our goal is to make it easy, fun, and social for anyone to start a
          fundraising campaign with Bitcoin.</h1>
      </div>
    </div>
  <? }
}