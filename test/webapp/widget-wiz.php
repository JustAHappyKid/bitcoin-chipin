<?php

namespace Chipin\Test;

require_once dirname(__FILE__) . '/harness.php';  # WebappTestingHarness
require_once 'chipin/widgets.php';                # Widget::getAll
require_once 'spare-parts/locales/countries.php'; # countriesMap
require_once 'spare-parts/database.php';          # query

use \Chipin\User, \Chipin\Widgets, \Chipin\Widgets\Widget, \SpareParts\Locales,
  \SpareParts\Test\HttpRedirect, \SpareParts\Test\ValidationErrors, \SpareParts\Database as DB,
  \SpareParts\WebClient\HtmlForm, \Exception, \DateTime;
use SpareParts\Test\UnexpectedHttpResponseCode;

class WidgetWizardTests extends WebappTestingHarness {

  private $user;

  function setUp() {
    parent::setUp();
    $this->user = $this->loginAsNormalUser();
    # Clear out the address-balance cache to assert a "new" address doesn't cause breakage...
    DB\query("DELETE FROM bitcoin_addresses WHERE address = ?", array($this->btcAddr()));
  }

  function testAddingAndEditingWidget() {
    $sizes = Widgets\allowedSizes();
    $colors = Widgets\allowedColors();

    # First we'll add a widget...
    $this->get('/widget-wiz/step-one');
    $expires = new DateTime("+3 days");
    $this->submitForm($this->getForm(),
      array('title' => 'Tengo hambre', 'goal' => '15', 'currency' => 'USD',
            'ending' => $expires->format("m/d/Y"), 'bitcoinAddress' => $this->btcAddr()));
    $this->submitForm($this->getForm(),
      array('about' => 'I need to get a bite to eat!', 'color' => $colors[0], 'size' => $sizes[0]));
    $widgets = Widget::getAll();
    assertEqual(1, count($widgets));
    $w = current($widgets);
    assertEqual('Tengo hambre', $w->title);
    assertEqual(15, (int) $w->goalAmnt->numUnits);
    assertEqual($expires->format('Y-m-d'), $w->ending->format('Y-m-d'));
    assertEqual($this->btcAddr(), $w->bitcoinAddress);
    assertEqual('I need to get a bite to eat!', $w->about);
    assertEqual($sizes[0]->width, (int) $w->width);
    assertEqual($sizes[0]->height, (int) $w->height);
    assertTrue(Locales\isValidCountryCode($w->countryCode),
      "'{$w->countryCode}' is not valid country-code");

    # Now we'll try editing that same widget
    $this->get("/widget-wiz/step-one?w={$w->id}");
    # Okay, maybe I only need ten bucks...
    $this->submitForm($this->getForm(), array('goal' => '10'));
    # Let's change the widget size and about-text too...
    $this->submitForm($this->getForm(),
      array('about' => "I've changed my mind.", 'size' => $sizes[1]));
    $widgetsNow = Widget::getAll();
    assertEqual(1, count($widgetsNow));
    $wNow = current($widgetsNow);
    assertEqual('Tengo hambre', $wNow->title);
    assertEqual(10, (int) $wNow->goalAmnt->numUnits);
    //assertEqual($expires, $wNow->ending);
    assertEqual($this->btcAddr(), $wNow->bitcoinAddress);
    assertEqual("I've changed my mind.", $wNow->about);
    assertEqual($sizes[1]->width, (int) $wNow->width);
    assertEqual($sizes[1]->height, (int) $wNow->height);
  }

  function testAddingWidgetAsUnauthenticatedUser() {
    $this->logout();
    $this->get('/widget-wiz/step-one');
    $expires = new DateTime("+50 days");
    $this->submitForm($this->getForm(),
      array('title' => 'What is Bitcoin?', 'goal' => '100', 'currency' => 'USD',
        'ending' => $expires->format("m/d/Y"), 'bitcoinAddress' => $this->btcAddr()));
    $this->submitForm($this->getForm(),
      array('about' => 'Donate some bitcoins so I can learn it.', 'color' => Widgets\defaultColor(),
            'size' => Widgets\defaultSize()));
    $widgets = Widget::getAll();
    assertEqual(1, count($widgets));
    $pass = 'sweet-corn-on-the-cob';
    $this->submitForm($this->getForm('signup-form'),
      array('email' => 'john@test.org', 'username' => 'butter-cookie',
            'password1' => $pass, 'password2' => $pass));
    $this->logout();
    $this->login('butter-cookie', $pass);
    $user = User::loadFromUsername('butter-cookie');
    $widgets = Widget::getManyByOwner($user);
    assertEqual(1, count($widgets));
    assertEqual('john@test.org', $user->email);
  }

  function testThatFieldsArePrePopulatedWhenEditingWidget() {
    $u = $this->loginAsNormalUser();
    $w = new Widget;
    $w->ownerID = $u->id;
    $w->title = 'Party Party';
    $w->about = 'Vamos a festejar.';
    $w->setGoal(50, 'CAD');
    $w->ending = '2015-12-31';
    $w->bitcoinAddress = $this->btcAddr();
    $sizes = Widgets\allowedSizes();
    $w->width = $sizes[0]->width;
    $w->height = $sizes[0]->height;
    $colors = Widgets\allowedColors();
    $w->color = $colors[0];
    $w->save();
    $this->get('/widget-wiz/step-one?w=' . $w->id);
    $titleInput = current($this->xpathQuery("//input[@name='title']"));
    assertEqual('Party Party', $titleInput->getAttribute('value'));
    $endDateInput = current($this->xpathQuery("//input[@name='ending']"));
    $this->assertDatesAreEqual($w->ending, $endDateInput->getAttribute('value'));
    $this->expectSpecificOption($fieldName = 'currency', $expectedValue = 'CAD');
    $this->submitForm($this->getForm(), array());
    $this->expectSpecificOption($fieldName = 'size', $expectedValue = $sizes[0]);
    $this->expectSpecificOption($fieldName = 'color', $expectedValue = $colors[0]);
  }

  function testWizardRejectsHtmlScriptTags() {
    $badContent = "<script>alert('!');</script>";
//    $this->loginAsNormalUser();
    $this->get('/widget-wiz/step-one');
    try {
      $this->submitForm($this->getForm(),
        array('title' => $badContent, 'goal' => '15', 'currency' => 'USD',
              'ending' => $this->in3days(), 'bitcoinAddress' => $this->btcAddr()));
      $this->submitForm($this->getForm(),
        array('about' => $badContent));
    } catch (Exception $_) {
      # XXX: Until we get proper form-validation in place, we'll just expect to see an
      # XXX: exception coming from the 'Paranoid' database layer proclaiming "No angle
      # XXX: brackets allowed!".
    }
    assertEqual(0, count(Widget::getAll()));
  }

  function testSupportForLargeGoals() {
    foreach (array(99, 600, 1000, 20000, 100000, 2000000) as $amount) {
      clearDB();
      try {
        $this->createWidget(array('goal' => (string)$amount));
        $ws = Widget::getAll();
        assertEqual($amount, (int) $ws[0]->goalAmnt->numUnits);
      } catch (ValidationErrors $e) {
        assertTrue(contains($e->getMessage(), "maximum"));
      }
    }
  }

  function testZeroAndNegativeValuesAreRejectedForGoal() {
    foreach (array(0, -1, -572) as $amount) {
      clearDB();
      try {
        $this->createWidget(array('goal' => (string)$amount));
        fail("Value of '$amount' should be rejected for 'goal' field");
      } catch (ValidationErrors $e) {
        /* That's what we want. */
      }
    }
  }

  function testSkippingStepOne() {
    $this->followRedirects(false);
    try {
      $this->get('/widget-wiz/step-two');
      $this->submitForm($this->getForm(), array('about' => 'I need money!'));
    } catch (HttpRedirect $e) {
      assertEqual('/widget-wiz/step-one', $e->path);
    }
    $this->clearSession();
    $this->loginAsNormalUser();
    try {
      $this->get('/widget-wiz/step-three');
      fail("Shouldn't be able to access step-three without defining widget");
    } catch (HttpRedirect $e) {
      assertEqual('/widget-wiz/step-one', $e->path);
    }
  }

  function testBitcoinAddressValidationAction() {
    assertEqual('true', $this->get('/widget-wiz/valid-btc-addr/' . $this->btcAddr())->content);
    assertEqual('false', $this->get('/widget-wiz/valid-btc-addr/peanuts')->content);
  }

  /**
   * For some reason the web-service FreeGeoIP.net does not return an appropriate value
   * for all IP addresses. In this case, when a lookup on IP 175.156.249.231 is done,
   * it returns "404 page not found".
   */
  function testUseCaseWhereIPAddressLookupServiceReturnsUnexpectedValue() {
    $w = getWidget($this->user);
    //$this->get("/widget-wiz/step-one?w={$w->id}");
    $this->get("/widget-wiz/step-two?w={$w->id}");
    $_POST = $this->getForm()->getDefaultValuesToSubmit();
    $this->makeRequest('POST', "/widget-wiz/step-two",
      $serverVars = array('REMOTE_ADDR' => '175.156.249.231'));
  }

  /**
   * Here we aim to assert we're not vulnerable to "CSRF" attacks. We do this simply by
   * asserting a "raw" POST request will not be accepted for widget editing, as this should
   * indicate the server is requiring some sort of "nonce" or "token" for accepting any
   * form submission. More on CSRF here:
   * https://www.owasp.org/index.php/Cross-Site_Request_Forgery_(CSRF)
   */
  function testResilienceToCrossSiteRequestForgeryAttack() {
    $w = getWidget($this->user);
    $this->get("/widget-wiz/step-one?w={$w->id}");
    try {
      $this->post("/widget-wiz/step-one",
        array('title' => 'Hijacked', 'goal' => '1000', 'currency' => 'USD',
              'ending' => "12/15/2020", 'bitcoinAddress' => '1E3FqrQTZSvTUdw7qZ4NnZppqiqnqqNcUN'));
    } catch (UnexpectedHttpResponseCode $_) { /* That will do... */ }
    try {
      $this->post("/widget-wiz/step-two",
        array('about' => 'Show me the money!', 'color' => Widgets\defaultColor(),
              'size' => Widgets\defaultSize()));
    } catch (UnexpectedHttpResponseCode $_) { /* That's good... */ }
    $widgetNow = Widget::getByID($w->id);
    assertNotEqual('Hijacked', $widgetNow->title);
    assertNotEqual('1E3FqrQTZSvTUdw7qZ4NnZppqiqnqqNcUN', $widgetNow->bitcoinAddress);
    assertNotEqual('Show me the money!', $widgetNow->about);
  }

  protected function getForm($formId = null) {
    return parent::getForm($formId ? $formId : 'widgetForm');
  }

  protected function submitForm(HtmlForm $form, Array $values = array(), $submitButton = null) {
    if ($submitButton == null) {
      $matches = array_filter($form->getButtons(), function($b) { return $b->id == 'next-step'; });
      $submitButton = count($matches) > 0 ? current($matches) : null;
    }
    return parent::submitForm($form, $values, $submitButton);
  }

  private function createWidget($attrs = array()) {
    $defaults = array('title' => 'Save the Queen', 'goal' => '275', 'currency' => 'USD',
      'ending' => date("Y-m-d", strtotime("+3 days")), 'bitcoinAddress' => $this->btcAddr(),
      'about' => "Before it's too late!", 'size' => '125x125', 'color' => 'A9DB80,96C56F');
    $attrs = array_merge($defaults, $attrs);
    $this->loginAsNormalUser();
    $this->get('/widget-wiz/step-one');
    $this->submitForm($this->getForm(),
      $this->takeValues($attrs, array('title', 'goal', 'currency', 'ending', 'bitcoinAddress')));
    $this->submitForm($this->getForm(),
      $this->takeValues($attrs, array('about', 'size', 'color')));
  }

  private function takeValues(Array $a, Array $vs) {
    return array_intersect_key($a, array_flip($vs));
  }

  private function in3days() { return date("Y-m-d", strtotime("+3 days")); }
//  private function defaultSize() { return '125x125'; }
//  private function defaultColor() { return 'A9DB80,96C56F'; }

  private function expectSpecificOption($fieldName, $expectedValue) {
    $selectedOptions = $this->xpathQuery("//select[@name='$fieldName']/option[@selected]");
    assertEqual(1, count($selectedOptions),
      "Exactly one option should be selected for field '$fieldName'");
    $selectedOption = current($selectedOptions);
    $selectedValue = $selectedOption->getAttribute('value');
    assertEqual(strval($expectedValue), $selectedValue,
      "Expected field '$fieldName' to have value '$expectedValue' selected but selected " .
      "value was '$selectedValue'");
  }

  private function btcAddr() { return '1PUPt26votHesaGwSApYtGVTfpzvs8AxVM'; }

  private function assertDatesAreEqual($d1, $d2) {
    if (empty($d1)) fail("d1 is empty");
    if (empty($d2)) fail("d2 is empty");
    $d1 = ($d1 instanceof DateTime) ? $d1 : new DateTime($d1);
    $d2 = ($d2 instanceof DateTime) ? $d2 : new DateTime($d2);
    assertEqual($d1->format('Y-m-d'), $d2->format('Y-m-d'));
  }
}

