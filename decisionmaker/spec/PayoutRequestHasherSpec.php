<?php

declare(strict_types = 1);

namespace decisionmaker\spec\asylgrp\decisionmaker;

use asylgrp\decisionmaker\PayoutRequest;
use asylgrp\decisionmaker\PayoutRequestHasher;
use asylgrp\decisionmaker\PayoutRequestCollection;
use asylgrp\decisionmaker\ContactPerson\ContactPersonInterface;
use asylgrp\decisionmaker\Grant\GrantInterface;
use byrokrat\banking\AccountNumber;
use byrokrat\amount\Currency\SEK;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class PayoutRequestHasherSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(PayoutRequestHasher::CLASS);
    }

    function it_can_hash(
        PayoutRequestCollection $collection,
        PayoutRequest $payout,
        ContactPersonInterface $contact,
        AccountNumber $account,
        GrantInterface $grant
    ) {
        $collection->getIterator()->willReturn((function () use ($payout) {
            yield $payout->getWrappedObject();
        })());

        $payout->getContactPerson()->willReturn($contact);

        $contact->getName()->willReturn('name')->shouldBeCalled();
        $contact->getAccount()->willReturn($account)->shouldBeCalled();

        $account->getNumber()->willReturn('accountnumber')->shouldBeCalled();

        $payout->getGrant()->willReturn($grant);

        $grant->getClaimDate()->willReturn(new \DateTimeImmutable)->shouldBeCalled();
        $grant->getClaimedAmount()->willReturn(new SEK('100'))->shouldBeCalled();
        $grant->getGrantedAmount()->willReturn(new SEK('50'))->shouldBeCalled();

        $this->hash($collection);
    }
}
