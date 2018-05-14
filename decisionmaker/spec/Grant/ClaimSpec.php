<?php

declare(strict_types = 1);

namespace decisionmaker\spec\asylgrp\decisionmaker\Grant;

use asylgrp\decisionmaker\Grant\Claim;
use asylgrp\decisionmaker\Grant\GrantInterface;
use byrokrat\amount\Amount;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ClaimSpec extends ObjectBehavior
{
    function let(\DateTimeImmutable $date, Amount $amount)
    {
        $this->beConstructedWith($date, $amount, '');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Claim::CLASS);
    }

    function it_implements_grant_interface()
    {
        $this->shouldHaveType(GrantInterface::CLASS);
    }

    function it_contains_a_date($date)
    {
        $this->getClaimDate()->shouldReturn($date);
    }

    function it_contains_a_claimed_amount($amount)
    {
        $this->getClaimedAmount()->shouldReturn($amount);
    }

    function it_contains_a_claim_description($amount)
    {
        $this->beConstructedWith(new \DateTimeImmutable, $amount, 'foobar');
        $this->getClaimDescription()->shouldReturn('foobar');
    }

    function it_contains_a_granted_amount()
    {
        $amount = new Amount('100');
        $this->beConstructedWith(new \DateTimeImmutable, $amount, '');
        $this->getGrantedAmount()->shouldBeLike(new Amount('0'));
    }

    function it_contains_a_not_granted_amount($amount)
    {
        $this->getNotGrantedAmount()->shouldReturn($amount);
    }

    function it_does_not_contain_any_grant_items()
    {
        $this->getGrantItems()->shouldIterateAs([]);
    }
}
