<?php

declare(strict_types = 1);

namespace decisionmaker\spec\asylgrp\decisionmaker;

use asylgrp\decisionmaker\Decision;
use asylgrp\decisionmaker\PayoutRequestCollection;
use asylgrp\decisionmaker\Grant\GrantInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use byrokrat\amount\Amount;

class DecisionSpec extends ObjectBehavior
{
    function let(PayoutRequestCollection $payouts)
    {
        $this->beConstructedWith('', new \DateTimeImmutable, new Amount('0'), $payouts);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Decision::CLASS);
    }

    function it_contains_an_id($payouts)
    {
        $this->beConstructedWith('foobar', new \DateTimeImmutable, new Amount('0'), $payouts);
        $this->getId()->shouldReturn('foobar');
    }

    function it_contains_a_date($payouts)
    {
        $date = new \DateTimeImmutable;
        $this->beConstructedWith('', $date, new Amount('0'), $payouts);
        $this->getDate()->shouldReturn($date);
    }

    function it_contains_a_pre_funds($payouts)
    {
        $amount = new Amount('100');
        $this->beConstructedWith('', new \DateTimeImmutable, $amount, $payouts);
        $this->getAllocatedAmount()->shouldReturn($amount);
    }

    function it_contains_payout_request_collection($payouts)
    {
        $this->getPayoutRequests()->shouldReturn($payouts);
    }
}
