<?php

namespace Chipin\Test;

require_once dirname(__FILE__) . '/harness.php';  # WebappTestingHarness
require_once 'chipin/widgets.php';                # Widget::getAll
require_once 'spare-parts/locales/countries.php'; # countriesMap
require_once 'spare-parts/database.php';          # query

use \Chipin\Widgets\Widget, \SpareParts\Locales, \Exception, \DateTime,
  \SpareParts\Test\HttpRedirect, \SpareParts\Test\ValidationErrors, \SpareParts\Database as DB;

class WidgetWizardTests extends WebappTestingHarness {

  function setUp() {
    parent::setUp();
    $this->loginAsNormalUser();
  }

  function testAddingAndEditingWidget() {
//    $this->loginAsNormalUser();

    # Clear out the address-balance cache to make sure a "new" address doesn't cause breakage...
    DB\query("DELETE FROM bitcoin_addresses WHERE address = ?", array($this->btcAddr()));

    # First we'll add a widget...
    $this->get('/widget-wiz/step-one');
    $expires = new DateTime("+3 days"); //date("Y-m-d", strtotime("+3 days"));
    $this->submitForm($this->getForm(),
      array('title' => 'Tengo hambre', 'goal' => '15', 'currency' => 'USD',
            'ending' => $expires->format("m/d/Y"), 'bitcoinAddress' => $this->btcAddr()));
    $this->submitForm($this->getForm(),
      array('about' => 'I need to get a bite to eat!'));
            // XXX
            //'size' => '125x125', 'color' => 'A9DB80,96C56F'));
    $widgets = Widget::getAll();
    assertEqual(1, count($widgets));
    $w = current($widgets);
    assertEqual('Tengo hambre', $w->title);
    assertEqual(15, (int) $w->goal);
    assertEqual($expires->format('Y-m-d'), $w->ending->format('Y-m-d'));
    assertEqual($this->btcAddr(), $w->bitcoinAddress);
    assertEqual('I need to get a bite to eat!', $w->about);
    /* XXX
    assertEqual(125, (int) $w->width);
    assertEqual(125, (int) $w->height);
    */
    assertTrue(Locales\isValidCountryCode($w->countryCode),
      "'{$w->countryCode}' is not valid country-code");

    # Now we'll try editing that same widget
    $this->get("/widget-wiz/step-one?w={$w->id}");
    # Okay, maybe I only need ten bucks...
    $this->submitForm($this->getForm(), array('goal' => '10'));
    # Let's change the widget size too...
    // XXX
    $this->submitForm($this->getForm(), array('about' => "I've changed my mind."));
    // $this->submitForm($this->getForm(), array('size' => '250x250'));
    $widgetsNow = Widget::getAll();
    assertEqual(1, count($widgetsNow));
    $wNow = current($widgetsNow);
    assertEqual('Tengo hambre', $wNow->title);
    assertEqual(10, (int) $wNow->goal);
    //assertEqual($expires, $wNow->ending);
    assertEqual($this->btcAddr(), $wNow->bitcoinAddress);
    assertEqual("I've changed my mind.", $wNow->about);
    /* XXX
    assertEqual(250, (int) $wNow->width);
    assertEqual(250, (int) $wNow->height);
    */
  }

  function testThatFieldsArePrePopulatedWhenEditingWidget() {
    $u = $this->loginAsNormalUser();
    $w = new Widget;
    $w->ownerID = $u->id;
    $w->title = 'Party Party';
    $w->about = 'Vamos a festejar.';
    $w->goal = 50;
    $w->currency = 'CAD';
    $w->ending = '2015-12-31';
    $w->bitcoinAddress = $this->btcAddr();
    $w->width = '220';
    $w->height = '220';
    $colorGrey = 'E0DCDC,707070';
    $w->color = $colorGrey;
    $w->save();
    $this->get('/widget-wiz/step-one?w=' . $w->id);
    $titleInput = current($this->xpathQuery("//input[@name='title']"));
    assertEqual('Party Party', $titleInput->getAttribute('value'));
    $endDateInput = current($this->xpathQuery("//input[@name='ending']"));
    $this->assertDatesAreEqual($w->ending, $endDateInput->getAttribute('value'));
    $this->expectSpecificOption($fieldName = 'currency', $expectedValue = 'CAD');
    $this->submitForm($this->getForm(), array());
    // XXX
    // $this->expectSpecificOption($fieldName = 'size', $expectedValue = '220x220');
    // $this->expectSpecificOption($fieldName = 'color', $expectedValue = $colorGrey);
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
              // 'size' => $this->defaultSize(), 'color' => $this->defaultColor()));
    } catch (Exception $_) {
      # XXX: Until we get proper form-validation in place, we'll just expect to see an
      #      exception coming from the 'Paranoid' database layer proclaiming "No angle
      #      brackets allowed!".
    }
    assertEqual(0, count(Widget::getAll()));
  }

  function testSupportForLargeGoals() {
    foreach (array(99, 600, 1000, 20000, 100000, 2000000) as $amount) {
      clearDB();
      try {
        $this->createWidget(array('goal' => (string)$amount));
        $ws = Widget::getAll();
        assertEqual($amount, (int) $ws[0]->goal);
      } catch (ValidationErrors $e) {
        assertTrue(contains($e->getMessage(), "maximum"));
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

  protected function getForm($formId = null) {
    return parent::getForm($formId ? $formId : 'widgetForm');
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
      $this->takeValues($attrs, array('about', /*'size', 'color'*/)));
  }

  private function takeValues(Array $a, Array $vs) {
    return array_intersect_key($a, array_flip($vs));
  }

  private function in3days() { return date("Y-m-d", strtotime("+3 days")); }
  private function defaultSize() { return '125x125'; }
  private function defaultColor() { return 'A9DB80,96C56F'; }

  private function expectSpecificOption($fieldName, $expectedValue) {
    $selectedOptions = $this->xpathQuery("//select[@name='$fieldName']/option[@selected]");
    assertEqual(1, count($selectedOptions),
      "Exactly one option should be selected for field '$fieldName'");
    $selectedOption = current($selectedOptions);
    assertEqual($expectedValue, $selectedOption->getAttribute('value'),
      "Expected field '$fieldName' to have value '$expectedValue' selected");
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

