<?php

declare(strict_types = 1);

namespace decisionmaker\spec\asylgrp\decisionmaker\Granter;

use asylgrp\decisionmaker\Granter\RatioGranter;
use asylgrp\decisionmaker\Granter\GranterInterface;
use asylgrp\decisionmaker\Grant\GrantInterface;
use asylgrp\decisionmaker\Grant\Grant;
use byrokrat\amount\Amount;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RatioGranterSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(0.0);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(RatioGranter::CLASS);
    }

    function it_implements_granter_interface()
    {
        $this->shouldHaveType(GranterInterface::CLASS);
    }

    function it_throws_on_too_big_ratio()
    {
        $this->beConstructedWith(1.1);
        $this->shouldThrow(\LogicException::CLASS)->duringInstantiation();
    }

    function it_throws_on_negative_ratio()
    {
        $this->beConstructedWith(-0.1);
        $this->shouldThrow(\LogicException::CLASS)->duringInstantiation();
    }

    function it_creates_grants(GrantInterface $decorate)
    {
        $this->beConstructedWith(0.5);

        $decorate->getNotGrantedAmount()->willReturn(new Amount('100'));

        $this->grant($decorate)->shouldBeLike(new Grant(
            $decorate->getWrappedObject(),
            new Amount('50'),
            'Added ratio: 100.00 * 0.5'
        ));
    }

    function it_rounds_down_grant(GrantInterface $decorate)
    {
        $this->beConstructedWith(0.3333);

        $decorate->getNotGrantedAmount()->willReturn(new Amount('100'));

        $this->grant($decorate)->shouldBeLike(new Grant(
            $decorate->getWrappedObject(),
            new Amount('33'),
            'Added ratio: 100.00 * 0.3333'
        ));
    }

    function it_ignores_zero_ratios(GrantInterface $decorate)
    {
        $this->beConstructedWith(0.0);
        $decorate->getNotGrantedAmount()->willReturn(new Amount('100'));
        $this->grant($decorate)->shouldReturn($decorate);
    }

    function it_ignores_zero_non_granted(GrantInterface $decorate)
    {
        $this->beConstructedWith(0.5);
        $decorate->getNotGrantedAmount()->willReturn(new Amount('0'));
        $this->grant($decorate)->shouldReturn($decorate);
    }
}
