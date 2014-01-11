<?php

namespace Chipin\WebFramework\Routes;

require_once 'chipin/widgets.php';
use \Chipin\Widgets\Widget;

//function addressBalance($a = null) { return '/bitcoin/address-balance/' . $a; }
function validAddress($a = null) { return '/bitcoin/valid-address/' . $a; }
function checkWidgetProgress(Widget $widget) { return "/widgets/progress/{$widget->id}"; }
function amountRaised(Widget $widget) { return "/widgets/amount-raised/{$widget->id}"; }
