<?php

declare(strict_types = 1);

namespace decisionmaker\spec\asylgrp\decisionmaker\Normalizer;

use asylgrp\decisionmaker\Normalizer\ContactNormalizer;
use asylgrp\decisionmaker\Contact;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ContactNormalizerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ContactNormalizer::CLASS);
    }

    function it_fails_normalizing_not_supported_objects()
    {
        $this->shouldThrow(\InvalidArgumentException::CLASS)->during('normalize', [(object)[]]);
    }

    function it_can_normalize(Contact $contact)
    {
        $this->normalize($contact)->shouldBeLike([
        ]);
    }

    function it_fails_denormalizing_not_supported_objects()
    {
        $this->shouldThrow(\InvalidArgumentException::CLASS)->during('normalize', [null, 'not-contact']);
    }

    function it_can_denormalize()
    {
        $data = [
        ];

        $this->denormalize($data, Contact::CLASS)->shouldBeLike(
            new Contact(
            )
        );
    }
}
