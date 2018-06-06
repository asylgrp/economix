<?php

declare(strict_types = 1);

namespace decisionmaker\spec\asylgrp\decisionmaker\Normalizer;

use asylgrp\decisionmaker\Normalizer\PayoutRequestNormalizer;
use asylgrp\decisionmaker\Normalizer\GrantNormalizer;
use asylgrp\decisionmaker\Normalizer\ContactPersonNormalizer;
use asylgrp\decisionmaker\PayoutRequest;
use asylgrp\decisionmaker\ContactPerson\ContactPersonInterface;
use asylgrp\decisionmaker\Grant\GrantInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class PayoutRequestNormalizerSpec extends ObjectBehavior
{
    function let(ContactPersonNormalizer $contactNormalizer, GrantNormalizer $grantNormalizer)
    {
        $this->beConstructedWith($contactNormalizer, $grantNormalizer);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PayoutRequestNormalizer::CLASS);
    }

    function it_fails_normalizing_not_supported_objects()
    {
        $this->shouldThrow(\InvalidArgumentException::CLASS)->during('normalize', [(object)[]]);
    }

    function it_can_normalize(
        $contactNormalizer,
        $grantNormalizer,
        PayoutRequest $payout,
        ContactPersonInterface $contact,
        GrantInterface $grant
    ) {
        $payout->getContactPerson()->willReturn($contact);
        $payout->getGrant()->willReturn($grant);

        $contactNormalizer->normalize($contact)->willReturn('CONTACT_NORMALIZED');
        $grantNormalizer->normalize($grant)->willReturn('GRANT_NORMALIZED');

        $this->normalize($payout)->shouldBeLike([
            'contact' => 'CONTACT_NORMALIZED',
            'grant' => 'GRANT_NORMALIZED',
        ]);
    }

    function it_fails_denormalizing_not_supported_objects()
    {
        $this->shouldThrow(\InvalidArgumentException::CLASS)->during('normalize', [null, 'no-payout']);
    }

    function it_can_denormalize($contactNormalizer, $grantNormalizer, ContactPersonInterface $contact, GrantInterface $grant)
    {
        $data = [
            'contact' => 'CONTACT_NORMALIZED',
            'grant' => 'GRANT_NORMALIZED',
        ];

        $contactNormalizer->denormalize('CONTACT_NORMALIZED', ContactPersonInterface::CLASS)->willReturn($contact);
        $grantNormalizer->denormalize('GRANT_NORMALIZED', GrantInterface::CLASS)->willReturn($grant);

        $this->denormalize($data, PayoutRequest::CLASS)->shouldBeLike(
            new PayoutRequest(
                $contact->getWrappedObject(),
                $grant->getWrappedObject()
            )
        );
    }
}
