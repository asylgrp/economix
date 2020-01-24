<?php

declare(strict_types = 1);

namespace decisionmaker\spec\asylgrp\decisionmaker\Grant;

use asylgrp\decisionmaker\Grant\Claim;
use asylgrp\decisionmaker\Grant\GrantInterface;
use Money\Money;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ClaimSpec extends ObjectBehavior
{
    function let(\DateTimeImmutable $date)
    {
        $this->beConstructedWith($date, Money::SEK('0'), '');
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

    function it_contains_a_claimed_amount()
    {
        $money = Money::SEK('100');
        $this->beConstructedWith(new \DateTimeImmutable, $money, '');
        $this->getClaimedAmount()->shouldReturn($money);
    }

    function it_contains_a_claim_description()
    {
        $this->beConstructedWith(new \DateTimeImmutable, Money::SEK('0'), 'foobar');
        $this->getClaimDescription()->shouldReturn('foobar');
    }

    function it_contains_a_granted_amount()
    {
        $money = Money::SEK('100');
        $this->beConstructedWith(new \DateTimeImmutable, $money, '');
        $this->getGrantedAmount()->shouldBeLike(Money::SEK('0'));
    }

    function it_contains_a_not_granted_amount()
    {
        $money = Money::SEK('100');
        $this->beConstructedWith(new \DateTimeImmutable, $money, '');
        $this->getNotGrantedAmount()->shouldReturn($money);
    }

    function it_does_not_contain_any_grant_items()
    {
        $this->getGrantItems()->shouldIterateAs([]);
    }
}
