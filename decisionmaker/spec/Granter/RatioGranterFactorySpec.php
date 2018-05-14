<?php

declare(strict_types = 1);

namespace decisionmaker\spec\asylgrp\decisionmaker\Granter;

use asylgrp\decisionmaker\Granter\RatioGranter;
use asylgrp\decisionmaker\Granter\RatioGranterFactory;
use asylgrp\decisionmaker\Granter\GranterFactoryInterface;
use asylgrp\decisionmaker\PayoutRequestCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use byrokrat\amount\Amount;

class RatioGranterFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(RatioGranterFactory::CLASS);
    }

    function it_implements_granter_factory_interface()
    {
        $this->shouldHaveType(GranterFactoryInterface::CLASS);
    }

    function it_creates_zero_ratio_if_there_is_nothing_to_grant(PayoutRequestCollection $payouts)
    {
        $payouts->getNotGrantedAmount()->willReturn(new Amount('0'));
        $this->createGranter(new Amount('1000'), $payouts)->shouldBeLike(new RatioGranter(0.0));
    }

    function it_calculates_ratio(PayoutRequestCollection $payouts)
    {
        $payouts->getNotGrantedAmount()->willReturn(new Amount('200'));
        $this->createGranter(new Amount('100'), $payouts)->shouldBeLike(new RatioGranter(0.5));
    }

    function it_uses_a_maximum_ratio_of_one(PayoutRequestCollection $payouts)
    {
        $payouts->getNotGrantedAmount()->willReturn(new Amount('100'));
        $this->createGranter(new Amount('200'), $payouts)->shouldBeLike(new RatioGranter(1.0));
    }
}
