<?php

namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

use Tests\_support\Helper\RandomTrait;

class Acceptance extends \Codeception\Module
{
    use RandomTrait;
}
