<?php

declare(strict_types = 1);

namespace asylgrp\decisionmaker\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use asylgrp\decisionmaker\PayoutRequest;
use asylgrp\decisionmaker\ContactPerson\ContactPersonInterface;
use asylgrp\decisionmaker\Grant\GrantInterface;

/**
 * Normalize PayoutRequest objects
 */
class PayoutRequestNormalizer implements NormalizerInterface, DenormalizerInterface
{
    private ContactPersonNormalizer $contactPersonNormalizer;
    private GrantNormalizer $grantNormalizer;

    public function __construct(
        ContactPersonNormalizer $contactPersonNormalizer = null,
        GrantNormalizer $grantNormalizer = null
    ) {
        $this->contactPersonNormalizer = $contactPersonNormalizer ?: new ContactPersonNormalizer;
        $this->grantNormalizer = $grantNormalizer ?: new GrantNormalizer;
    }

    /**
     * @param mixed $obj
     */
    public function supportsNormalization($obj, ?string $format = null): bool
    {
        return $obj instanceof PayoutRequest;
    }

    /**
     * @param mixed $obj
     * @param array<mixed> $cntxt
     * @return array<string, array>
     */
    public function normalize($obj, ?string $format = null, array $cntxt = []): array
    {
        if (!$this->supportsNormalization($obj, $format)) {
            throw new \InvalidArgumentException('Unable to normalize, expecting PayoutRequest');
        }

        /** @var PayoutRequest $obj */

        return [
            'contact' => $this->contactPersonNormalizer->normalize($obj->getContactPerson()),
            'grant' => $this->grantNormalizer->normalize($obj->getGrant())
        ];
    }

    /**
     * @param array<string, array> $data
     */
    public function supportsDenormalization($data, string $type, ?string $format = null): bool
    {
        return $type == PayoutRequest::CLASS;
    }

    /**
     * @param array<string, array> $data
     * @param array<mixed> $cntxt
     */
    public function denormalize($data, string $type, ?string $format = null, array $cntxt = []): PayoutRequest
    {
        if (!$this->supportsDenormalization($data, $type, $format)) {
            throw new \InvalidArgumentException('Unable to denormalize, expecting PayoutRequest');
        }

        return new PayoutRequest(
            $this->contactPersonNormalizer->denormalize($data['contact'], ContactPersonInterface::CLASS),
            $this->grantNormalizer->denormalize($data['grant'], GrantInterface::CLASS)
        );
    }
}
