<?php

declare(strict_types = 1);

namespace decisionmaker\spec\asylgrp\decisionmaker\Normalizer;

use asylgrp\decisionmaker\Normalizer\GrantNormalizer;
use asylgrp\decisionmaker\Grant\GrantInterface;
use asylgrp\decisionmaker\Grant\GrantItem;
use asylgrp\decisionmaker\Grant\Claim;
use asylgrp\decisionmaker\Grant\Grant;
use byrokrat\amount\Currency\SEK;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class GrantNormalizerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(GrantNormalizer::CLASS);
    }

    function it_fails_normalizing_not_supported_objects()
    {
        $this->shouldThrow(\InvalidArgumentException::CLASS)->during('normalize', [(object)[]]);
    }

    function it_can_normalize(GrantInterface $grant, GrantItem $item)
    {
        $item->getGrantedAmount()->willReturn(new SEK('50'));
        $item->getGrantDescription()->willReturn('foo');

        $grant->getGrantItems()->willReturn((function () use ($item) {
            yield $item->getWrappedObject();
        })());

        $normalizedDate = "2018-05-22T12:19:53+02:00";

        $grant->getClaimDate()->willReturn(\DateTimeImmutable::createFromFormat(DATE_W3C, $normalizedDate));
        $grant->getClaimedAmount()->willReturn(new SEK('100'));
        $grant->getClaimDescription()->willReturn('bar');

        $this->normalize($grant)->shouldBeLike([
            'claim_date' => $normalizedDate,
            'claimed_amount' => "100",
            'claim_description' => "bar",
            'grant_items' => [
                [
                    'granted_amount' => "50",
                    'grant_description' => "foo",
                ],
            ]
        ]);
    }

    function it_fails_denormalizing_not_supported_objects()
    {
        $this->shouldThrow(\InvalidArgumentException::CLASS)->during('normalize', [null, 'not-grant-interface']);
    }

    function it_can_denormalize()
    {
        $normalizedDate = "2018-05-22T12:19:53+02:00";

        $data = [
            'claim_date' => $normalizedDate,
            'claimed_amount' => "100",
            'claim_description' => "bar",
            'grant_items' => [
                [
                    'granted_amount' => "50",
                    'grant_description' => "foo",
                ],
            ]
        ];

        $this->denormalize($data, GrantInterface::CLASS)->shouldBeLike(
            new Grant(
                new Claim(
                    \DateTimeImmutable::createFromFormat(DATE_W3C, $normalizedDate),
                    new SEK('100'),
                    'bar'
                ),
                new SEK('50'),
                'foo'
            )
        );
    }
}
