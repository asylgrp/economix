<?php

declare(strict_types = 1);

namespace decisionmaker\spec\asylgrp\decisionmaker\Grant;

use asylgrp\decisionmaker\Grant\GrantItem;
use Money\Money;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class GrantItemSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(Money::SEK('0'), 'foobar');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(GrantItem::CLASS);
    }

    function it_contains_an_amount()
    {
        $money = Money::SEK('1');
        $this->beConstructedWith($money, 'foobar');
        $this->getGrantedAmount()->shouldReturn($money);
    }

    function it_contains_a_description()
    {
        $this->getGrantDescription()->shouldReturn('foobar');
    }
}
