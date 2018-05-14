<?php

declare(strict_types = 1);

namespace decisionmaker\spec\asylgrp\decisionmaker\Grant;

use asylgrp\decisionmaker\Grant\GrantItem;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use byrokrat\amount\Amount;

class GrantItemSpec extends ObjectBehavior
{
    function let(Amount $amount)
    {
        $this->beConstructedWith($amount, 'foobar');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(GrantItem::CLASS);
    }

    function it_contains_an_amount($amount)
    {
        $this->getGrantedAmount()->shouldReturn($amount);
    }

    function it_contains_a_description()
    {
        $this->getGrantDescription()->shouldReturn('foobar');
    }
}
