<?php

declare(strict_types = 1);

namespace decisionmaker\spec\asylgrp\decisionmaker\Granter;

use asylgrp\decisionmaker\Granter\FixedGranter;
use asylgrp\decisionmaker\Granter\FixedGranterFactory;
use asylgrp\decisionmaker\Granter\GranterFactoryInterface;
use asylgrp\decisionmaker\PayoutRequestCollection;
use Money\Money;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class FixedGranterFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(FixedGranterFactory::CLASS);
    }

    function it_implements_granter_factory_interface()
    {
        $this->shouldHaveType(GranterFactoryInterface::CLASS);
    }

    function it_can_calculate_fixed_amount(PayoutRequestCollection $payouts)
    {
        $payouts->count()->willReturn(2);

        $this->createGranter(Money::SEK('100'), $payouts)->shouldBeLike(
            new FixedGranter(Money::SEK('50'))
        );
    }

    function it_can_round_fixed_amount(PayoutRequestCollection $payouts)
    {
        $payouts->count()->willReturn(3);

        $this->createGranter(Money::SEK('100'), $payouts)->shouldBeLike(
            new FixedGranter(Money::SEK('33'))
        );
    }

    function it_can_set_max_amount(PayoutRequestCollection $payouts)
    {
        $payouts->count()->willReturn(2);

        $this->beConstructedWith(Money::SEK('25'));

        $this->createGranter(Money::SEK('100'), $payouts)->shouldBeLike(
            new FixedGranter(Money::SEK('25'))
        );
    }

    function it_ignores_max_amount_when_not_applicable(PayoutRequestCollection $payouts)
    {
        $payouts->count()->willReturn(2);

        $this->beConstructedWith(Money::SEK('100'));

        $this->createGranter(Money::SEK('100'), $payouts)->shouldBeLike(
            new FixedGranter(Money::SEK('50'))
        );
    }
}
