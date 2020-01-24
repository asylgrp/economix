<?php

declare(strict_types = 1);

namespace decisionmaker\spec\asylgrp\decisionmaker\Grant;

use asylgrp\decisionmaker\Grant\Grant;
use asylgrp\decisionmaker\Grant\GrantInterface;
use asylgrp\decisionmaker\Grant\GrantItem;
use Money\Money;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class GrantSpec extends ObjectBehavior
{
    function let(GrantInterface $decorated)
    {
        $decorated->getClaimedAmount()->willReturn(Money::SEK('0'));
        $decorated->getGrantedAmount()->willReturn(Money::SEK('0'));
        $decorated->getNotGrantedAmount()->willReturn(Money::SEK('0'));
        $this->beConstructedWith($decorated, Money::SEK('0'), '');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Grant::CLASS);
    }

    function it_implements_grant_interface()
    {
        $this->shouldHaveType(GrantInterface::CLASS);
    }

    function it_passes_decorated_claim_date($decorated, \DateTimeImmutable $claimDate)
    {
        $decorated->getClaimDate()->willReturn($claimDate);
        $this->getClaimDate()->shouldReturn($claimDate);
    }

    function it_passes_decorated_claim_amount($decorated)
    {
        $claim = Money::SEK('1');
        $decorated->getClaimedAmount()->willReturn($claim);
        $this->getClaimedAmount()->shouldReturn($claim);
    }

    function it_passes_decorated_claim_desc($decorated)
    {
        $decorated->getClaimDescription()->willReturn('foobar');
        $this->getClaimDescription()->shouldReturn('foobar');
    }

    function it_adds_this_amount_to_granted_amount($decorated)
    {
        $decorated->getGrantedAmount()->willReturn(Money::SEK('100'));
        $decorated->getNotGrantedAmount()->willReturn(Money::SEK('100'));
        $this->beConstructedWith($decorated, Money::SEK('100'), '');
        $this->getGrantedAmount()->shouldBeLike(Money::SEK('200'));
    }

    function it_does_not_add_more_than_not_granted_amount($decorated)
    {
        $decorated->getGrantedAmount()->willReturn(Money::SEK('100'));
        $decorated->getNotGrantedAmount()->willReturn(Money::SEK('50'));
        $this->beConstructedWith($decorated, Money::SEK('100'), '');
        $this->getGrantedAmount()->shouldBeLike(Money::SEK('150'));
    }

    function it_can_calculate_not_granted_amount($decorated)
    {
        $decorated->getClaimedAmount()->willReturn(Money::SEK('300'));
        $decorated->getGrantedAmount()->willReturn(Money::SEK('0'));
        $decorated->getNotGrantedAmount()->willReturn(Money::SEK('300'));
        $this->beConstructedWith($decorated, Money::SEK('100'), '');
        $this->getNotGrantedAmount()->shouldBeLike(Money::SEK('200'));
    }

    function it_passes_amount_and_desc_to_grant_item($decorated)
    {
        $amount = Money::SEK('0');
        $this->beConstructedWith($decorated, $amount, 'foobar');
        $decorated->getGrantItems()->willReturn((function () {
            yield from [];
        })());
        $this->getGrantItems()->shouldReturnGrantItems([new GrantItem($amount, 'foobar')]);
    }

    public function getMatchers(): array
    {
        return [
            'returnGrantItems' => function($subject, $expected) {
                foreach ($subject as $index => $returnedItem) {
                    $expectedItem = $expected[$index];
                    if ($returnedItem->getGrantedAmount() !== $expectedItem->getGrantedAmount()) {
                        return false;
                    }
                    if ($returnedItem->getGrantDescription() !== $expectedItem->getGrantDescription()) {
                        return false;
                    }
                }

                return true;
            }
        ];
    }
}
