<?php

declare(strict_types = 1);

namespace decisionmaker\spec\asylgrp\decisionmaker;

use asylgrp\decisionmaker\PayoutRequestFactory;
use asylgrp\decisionmaker\PayoutRequest;
use asylgrp\decisionmaker\ContactPerson\ContactPersonInterface;
use asylgrp\decisionmaker\Grant\Claim;
use Lcobucci\Clock\Clock;
use Money\Money;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class PayoutRequestFactorySpec extends ObjectBehavior
{
    function let(Clock $clock)
    {
        $this->beConstructedWith($clock);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PayoutRequestFactory::CLASS);
    }

    function it_fails_if_contact_is_not_active(ContactPersonInterface $contact)
    {
        $contact->isActive()->willReturn(false);
        $this->shouldThrow(\LogicException::CLASS)->duringRequestPayout($contact, Money::SEK('100'), '');
    }

    function it_can_request_payout($clock, ContactPersonInterface $contact)
    {
        $contact->isActive()->willReturn(true);
        $clock->now()->willReturn($date = new \DateTimeImmutable);
        $this->requestPayout($contact, Money::SEK('100'), 'desc')->shouldBeLike(
            new PayoutRequest(
                $contact->getWrappedObject(),
                new Claim(
                    $date,
                    Money::SEK('100'),
                    'desc'
                )
            )
        );
    }
}
