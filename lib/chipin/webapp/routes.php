<?php

namespace Chipin\WebFramework\Routes;

function addressBalance($a = null) { return '/bitcoin/address-balance/' . $a; }
function validAddress($a = null) { return '/bitcoin/valid-address/' . $a; }
