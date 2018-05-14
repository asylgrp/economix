<?php

declare(strict_types = 1);

namespace decisionmaker\spec\asylgrp\decisionmaker;

use asylgrp\decisionmaker\PayoutRequestCollection;
use asylgrp\decisionmaker\PayoutRequest;
use asylgrp\decisionmaker\Grant\GrantInterface;
use byrokrat\amount\Amount;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class PayoutRequestCollectionSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith([]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PayoutRequestCollection::CLASS);
    }

    function it_contains_payout_requests(PayoutRequest $payout)
    {
        $this->beConstructedWith([$payout, $payout]);
        $this->getIterator()->shouldIterateAs([$payout, $payout]);
    }

    function it_can_count_payout_requests(PayoutRequest $payout)
    {
        $this->beConstructedWith([$payout, $payout]);
        $this->count()->shouldReturn(2);
    }

    function it_can_summarize_claimed_amount(PayoutRequest $payout, GrantInterface $grant)
    {
        $this->beConstructedWith([$payout, $payout]);

        $payout->getGrant()->willReturn($grant);
        $grant->getClaimedAmount()->willReturn(new Amount('50'));

        $this->getClaimedAmount()->shouldBeLike(new Amount('100'));
    }

    function it_can_summarize_granted_amount(PayoutRequest $payout, GrantInterface $grant)
    {
        $this->beConstructedWith([$payout, $payout]);

        $payout->getGrant()->willReturn($grant);
        $grant->getGrantedAmount()->willReturn(new Amount('25'));

        $this->getGrantedAmount()->shouldBeLike(new Amount('50'));
    }

    function it_can_summarize_not_granted_amount(PayoutRequest $payout, GrantInterface $grant)
    {
        $this->beConstructedWith([$payout, $payout]);

        $payout->getGrant()->willReturn($grant);
        $grant->getNotGrantedAmount()->willReturn(new Amount('25'));

        $this->getNotGrantedAmount()->shouldBeLike(new Amount('50'));
    }
}
