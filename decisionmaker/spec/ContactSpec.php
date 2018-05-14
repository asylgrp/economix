<?php

declare(strict_types = 1);

namespace decisionmaker\spec\asylgrp\decisionmaker;

use asylgrp\decisionmaker\Contact;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ContactSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Contact::CLASS);
    }
}
