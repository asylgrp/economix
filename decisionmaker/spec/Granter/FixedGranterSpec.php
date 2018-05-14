<?php

declare(strict_types = 1);

namespace decisionmaker\spec\asylgrp\decisionmaker\Granter;

use asylgrp\decisionmaker\Granter\FixedGranter;
use asylgrp\decisionmaker\Granter\GranterInterface;
use asylgrp\decisionmaker\Grant\GrantInterface;
use asylgrp\decisionmaker\Grant\Grant;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use byrokrat\amount\Amount;

class FixedGranterSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(new Amount('0'));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(FixedGranter::CLASS);
    }

    function it_implements_granter_interface()
    {
        $this->shouldHaveType(GranterInterface::CLASS);
    }

    function it_creates_fixed_grant(GrantInterface $decorate)
    {
        $amount = new Amount('100');
        $this->beConstructedWith($amount);

        $decorate->getNotGrantedAmount()->willReturn(new Amount('0'));

        $this->grant($decorate)->shouldBeLike(new Grant(
            $decorate->getWrappedObject(),
            $amount,
            'Added a fixed grant of 100.00'
        ));
    }
}
