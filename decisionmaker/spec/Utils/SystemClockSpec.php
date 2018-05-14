<?php

declare(strict_types = 1);

namespace decisionmaker\spec\asylgrp\decisionmaker\Utils;

use asylgrp\decisionmaker\Utils\SystemClock;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class SystemClockSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(SystemClock::CLASS);
    }

    function it_can_create_now()
    {
        $this->now()->shouldHaveType(\DateTimeImmutable::CLASS);
    }
}
