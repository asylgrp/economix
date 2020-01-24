<?php

declare(strict_types = 1);

namespace decisionmaker\spec\asylgrp\decisionmaker\Granter;

use asylgrp\decisionmaker\Granter\RatioGranter;
use asylgrp\decisionmaker\Granter\RatioGranterFactory;
use asylgrp\decisionmaker\Granter\GranterFactoryInterface;
use asylgrp\decisionmaker\PayoutRequestCollection;
use Money\Money;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

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
        $payouts->getNotGrantedAmount()->willReturn(Money::SEK('0'));
        $this->createGranter(Money::SEK('1000'), $payouts)->shouldBeLike(new RatioGranter(0.0));
    }

    function it_calculates_ratio(PayoutRequestCollection $payouts)
    {
        $payouts->getNotGrantedAmount()->willReturn(Money::SEK('200'));
        $this->createGranter(Money::SEK('100'), $payouts)->shouldBeLike(new RatioGranter(0.5));
    }

    function it_uses_a_maximum_ratio_of_one(PayoutRequestCollection $payouts)
    {
        $payouts->getNotGrantedAmount()->willReturn(Money::SEK('100'));
        $this->createGranter(Money::SEK('200'), $payouts)->shouldBeLike(new RatioGranter(1.0));
    }
}
