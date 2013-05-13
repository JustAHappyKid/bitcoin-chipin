<?php

namespace Chipin\Test;

// require_once 'my-php-libs/database.php';
require_once 'chipin/widgets.php';  # Widget::getAll

// use \MyPHPLibs\Database as DB;
use \Chipin\Widgets\Widget;

class WidgetWizardTests extends WebappTestingHarness {

  function setUp() {
    clearDB();
    $this->followRedirects();
  }

  function testAddingAndEditingWidget() {
    $this->loginAsNormalUser();

    # First we'll add a widget...
    $this->get('/widget-wiz/step-one');
    $expires = date("Y-m-d", strtotime("+3 days"));
    $this->submitForm($this->getForm(),
      array('title' => 'Tengo hambre', 'goal' => '15', 'currency' => 'USD',
            'ending' => $expires, 'bitcoinAddress' => $this->btcAddr()));
    $this->submitForm($this->getForm(),
      array('about' => 'I need to get a bite to eat!', 'size' => '125x125',
            'color' => 'A9DB80,96C56F'));
    $widgets = Widget::getAll();
    assertEqual(1, count($widgets));
    $w = current($widgets);
    assertEqual('Tengo hambre', $w->title);
    assertEqual(15, (int) $w->goal);
    assertEqual($this->btcAddr(), $w->bitcoinAddress);
    assertEqual('I need to get a bite to eat!', $w->about);
    assertEqual(125, (int) $w->width);
    assertEqual(125, (int) $w->height);

    # Now we'll try editing that same widget
    $this->get("/widget-wiz/step-one?w={$w->id}");
    # Okay, maybe I only need ten bucks...
    $this->submitForm($this->getForm(), array('goal' => '10'));
    # Let's change the widget size too...
    $this->submitForm($this->getForm(), array('size' => '250x250'));
    $widgetsNow = Widget::getAll();
    assertEqual(1, count($widgetsNow));
    $wNow = current($widgetsNow);
    assertEqual('Tengo hambre', $wNow->title);
    assertEqual(10, (int) $wNow->goal);
    //assertEqual($expires, $wNow->ending);
    assertEqual($this->btcAddr(), $wNow->bitcoinAddress);
    assertEqual('I need to get a bite to eat!', $wNow->about);
    assertEqual(250, (int) $wNow->width);
    assertEqual(250, (int) $wNow->height);
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
    $w->address = $this->btcAddr();
    $w->width = '220';
    $w->height = '220';
    $colorGrey = 'E0DCDC,707070';
    $w->color = $colorGrey;
    $w->save();
    $this->get('/widget-wiz/step-one?w=' . $w->id);
    $titleInput = current($this->xpathQuery("//input[@name='title']"));
    assertEqual('Party Party', $titleInput->getAttribute('value'));
    $this->expectSpecificOption($fieldName = 'currency', $expectedValue = 'CAD');
    $this->submitForm($this->getForm(), array());
    $this->expectSpecificOption($fieldName = 'size', $expectedValue = '220x220');
    $this->expectSpecificOption($fieldName = 'color', $expectedValue = $colorGrey);
  }

  private function expectSpecificOption($fieldName, $expectedValue) {
    $selectedOptions = $this->xpathQuery("//select[@name='$fieldName']/option[@selected]");
    assertEqual(1, count($selectedOptions),
      "Exactly one option should be selected for field '$fieldName'");
    $selectedOption = current($selectedOptions);
    assertEqual($expectedValue, $selectedOption->getAttribute('value'),
      "Expected field '$fieldName' to have value '$expectedValue' selected");
  }

  private function btcAddr() { return '1PUPt26votHesaGwSApYtGVTfpzvs8AxVM'; }
}
