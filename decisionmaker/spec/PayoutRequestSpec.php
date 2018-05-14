<?php

declare(strict_types = 1);

namespace decisionmaker\spec\asylgrp\decisionmaker;

use asylgrp\decisionmaker\Contact;
use asylgrp\decisionmaker\PayoutRequest;
use asylgrp\decisionmaker\Grant\GrantInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class PayoutRequestSpec extends ObjectBehavior
{
    function let(Contact $contact, GrantInterface $grant)
    {
        $this->beConstructedWith($contact, $grant);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PayoutRequest::CLASS);
    }

    function it_contains_contact($contact)
    {
        $this->getContact()->shouldReturn($contact);
    }

    function it_contains_grant($grant)
    {
        $this->getGrant()->shouldReturn($grant);
    }

    function it_can_make_a_new_request_from_grant($contact, GrantInterface $newGrant)
    {
        $this->withGrant($newGrant)->shouldBeLike(new PayoutRequest(
            $contact->getWrappedObject(),
            $newGrant->getWrappedObject()
        ));
    }
}
