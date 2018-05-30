<?php

declare(strict_types = 1);

namespace asylgrp\decisionmaker\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use asylgrp\decisionmaker\PayoutRequest;
use asylgrp\decisionmaker\Contact;
use asylgrp\decisionmaker\Grant\GrantInterface;

/**
 * Normalize PayoutRequest objects
 */
class PayoutRequestNormalizer implements NormalizerInterface, DenormalizerInterface
{
    /**
     * @var ContactNormalizer
     */
    private $contactNormalizer;

    /**
     * @var GrantNormalizer
     */
    private $grantNormalizer;

    public function __construct(ContactNormalizer $contactNormalizer = null, GrantNormalizer $grantNormalizer = null)
    {
        $this->contactNormalizer = $contactNormalizer ?: new ContactNormalizer;
        $this->grantNormalizer = $grantNormalizer ?: new GrantNormalizer;
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof PayoutRequest;
    }

    public function normalize($payout, $format = null, array $context = [])
    {
        if (!$this->supportsNormalization($payout, $format)) {
            throw new \InvalidArgumentException('Unable to normalize, expecting PayoutRequest');
        }

        return [
            'contact' => $this->contactNormalizer->normalize($payout->getContact()),
            'grant' => $this->grantNormalizer->normalize($payout->getGrant())
        ];
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        return $type == PayoutRequest::CLASS;
    }

    public function denormalize($data, $type, $format = null, array $context = [])
    {
        if (!$this->supportsDenormalization($data, $type, $format)) {
            throw new \InvalidArgumentException('Unable to denormalize, expecting PayoutRequest');
        }

        return new PayoutRequest(
            $this->contactNormalizer->denormalize($data['contact'], Contact::CLASS),
            $this->grantNormalizer->denormalize($data['grant'], GrantInterface::CLASS)
        );
    }
}
