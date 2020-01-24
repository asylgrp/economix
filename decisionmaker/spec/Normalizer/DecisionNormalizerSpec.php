<?php

declare(strict_types = 1);

namespace decisionmaker\spec\asylgrp\decisionmaker\Normalizer;

use asylgrp\decisionmaker\Normalizer\DecisionNormalizer;
use asylgrp\decisionmaker\Normalizer\PayoutRequestNormalizer;
use asylgrp\decisionmaker\PayoutRequest;
use asylgrp\decisionmaker\PayoutRequestCollection;
use asylgrp\decisionmaker\Decision;
use Money\Money;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class DecisionNormalizerSpec extends ObjectBehavior
{
    function let(PayoutRequestNormalizer $payoutRequestNormalizer)
    {
        $this->beConstructedWith($payoutRequestNormalizer);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(DecisionNormalizer::CLASS);
    }

    function it_fails_normalizing_not_supported_objects()
    {
        $this->shouldThrow(\InvalidArgumentException::CLASS)->during('normalize', [(object)[]]);
    }

    function it_can_normalize(
        $payoutRequestNormalizer,
        Decision $decision,
        PayoutRequestCollection $payoutRequestCollection,
        PayoutRequest $payoutA,
        PayoutRequest $payoutB
    ) {
        $decision->getId()->willReturn('foo');
        $decision->getSignature()->willReturn('bar');

        $normalizedDate = "2018-05-22T12:19:53+02:00";
        $decision->getDate()->willReturn(\DateTimeImmutable::createFromFormat(DATE_W3C, $normalizedDate));

        $normalizedAmount = '100';
        $decision->getAllocatedAmount()->willReturn(Money::SEK($normalizedAmount));

        $payoutRequestCollection->getIterator()->willReturn((function () use ($payoutA, $payoutB) {
            yield $payoutA;
            yield $payoutB;
        })());

        $decision->getPayoutRequests()->willReturn($payoutRequestCollection);

        $payoutRequestNormalizer->normalize($payoutA)->willReturn(['NORMALIZED_PAYOUT_A']);
        $payoutRequestNormalizer->normalize($payoutB)->willReturn(['NORMALIZED_PAYOUT_B']);

        $this->normalize($decision)->shouldBeLike([
            'id' => 'foo',
            'signature' => 'bar',
            'date' => $normalizedDate,
            'allocated_amount' => $normalizedAmount,
            'payouts' => [
                ['NORMALIZED_PAYOUT_A'],
                ['NORMALIZED_PAYOUT_B'],
            ]
        ]);
    }

    function it_fails_denormalizing_not_supported_objects()
    {
        $this->shouldThrow(\InvalidArgumentException::CLASS)->during('normalize', [null, 'not-decision']);
    }

    function it_can_denormalize($payoutRequestNormalizer, PayoutRequest $payoutA, PayoutRequest $payoutB)
    {
        $normalizedDate = "2018-05-22T12:19:53+02:00";
        $normalizedAmount = '100';

        $data = [
            'id' => 'foo',
            'signature' => 'bar',
            'date' => $normalizedDate,
            'allocated_amount' => $normalizedAmount,
            'payouts' => [
                'NORMALIZED_PAYOUT_A',
                'NORMALIZED_PAYOUT_B',
            ]
        ];

        $payoutRequestNormalizer->denormalize('NORMALIZED_PAYOUT_A', PayoutRequest::CLASS)->willReturn($payoutA);
        $payoutRequestNormalizer->denormalize('NORMALIZED_PAYOUT_B', PayoutRequest::CLASS)->willReturn($payoutB);

        $this->denormalize($data, Decision::CLASS)->shouldBeLike(
            new Decision(
                'foo',
                'bar',
                \DateTimeImmutable::createFromFormat(DATE_W3C, $normalizedDate),
                Money::SEK($normalizedAmount),
                new PayoutRequestCollection([
                    $payoutA->getWrappedObject(),
                    $payoutB->getWrappedObject(),
                ])
            )
        );
    }
}
