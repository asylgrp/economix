<?php

declare(strict_types = 1);

namespace asylgrp\decisionmaker\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use asylgrp\decisionmaker\Decision;
use asylgrp\decisionmaker\PayoutRequest;
use asylgrp\decisionmaker\PayoutRequestCollection;

/**
 * Normalize decisions objects
 */
class DecisionNormalizer implements NormalizerInterface, DenormalizerInterface
{
    use HelperTrait;

    /**
     * @var PayoutRequestNormalizer
     */
    private $payoutRequestNormalizer;

    public function __construct(PayoutRequestNormalizer $payoutRequestNormalizer = null)
    {
        $this->payoutRequestNormalizer = $payoutRequestNormalizer ?: new PayoutRequestNormalizer;
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Decision;
    }

    public function normalize($obj, $format = null, $context = [])
    {
        if (!$this->supportsNormalization($obj, $format)) {
            throw new \InvalidArgumentException('Unable to normalize, expecting Decision');
        }

        return [
            'id' => $obj->getId(),
            'signature' => $obj->getSignature(),
            'date' => $this->normalizeDate($obj->getDate()),
            'allocated_amount' => $this->normalizeAmount($obj->getAllocatedAmount()),
            'payouts' => array_map(
                function ($payout) {
                    return $this->payoutRequestNormalizer->normalize($payout);
                },
                iterator_to_array($obj->getPayoutRequests())
            )
        ];
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        return $type == Decision::CLASS;
    }

    public function denormalize($data, $type, $format = null, $context = [])
    {
        if (!$this->supportsDenormalization($data, $type, $format)) {
            throw new \InvalidArgumentException('Unable to denormalize, expecting Decision');
        }

        return new Decision(
            $data['id'],
            $data['signature'],
            $this->denormalizeDate($data['date']),
            $this->denormalizeAmount($data['allocated_amount']),
            new PayoutRequestCollection(
                array_map(
                    function ($payoutData) {
                        return $this->payoutRequestNormalizer->denormalize($payoutData, PayoutRequest::CLASS);
                    },
                    $data['payouts']
                )
            )
        );
    }
}
