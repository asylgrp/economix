<?php

declare(strict_types = 1);

namespace asylgrp\decisionmaker\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use asylgrp\decisionmaker\Grant\GrantInterface;
use asylgrp\decisionmaker\Grant\Claim;
use asylgrp\decisionmaker\Grant\Grant;

/**
 * Normalize grant objects
 */
class GrantNormalizer implements NormalizerInterface, DenormalizerInterface
{
    use HelperTrait;

    /**
     * @param mixed $obj
     */
    public function supportsNormalization($obj, ?string $format = null): bool
    {
        return $obj instanceof GrantInterface;
    }

    /**
     * @param mixed $obj
     * @param array<mixed> $cntxt
     * @return array<string, mixed>
     */
    public function normalize($obj, ?string $format = null, array $cntxt = []): array
    {
        if (!$this->supportsNormalization($obj, $format)) {
            throw new \InvalidArgumentException('Unable to normalize, expecting GrantInterface');
        }

        /** @var GrantInterface $obj */

        $grantItems = [];

        foreach ($obj->getGrantItems() as $grantItem) {
            $grantItems[] = [
                'granted_amount' => $this->normalizeAmount($grantItem->getGrantedAmount()),
                'grant_description' => $grantItem->getGrantDescription()
            ];
        }

        return [
            'claim_date' => $this->normalizeDate($obj->getClaimDate()),
            'claimed_amount' => $this->normalizeAmount($obj->getClaimedAmount()),
            'claim_description' => $obj->getClaimDescription(),
            'grant_items' => $grantItems
        ];
    }

    /**
     * @param array<string, mixed> $data
     */
    public function supportsDenormalization($data, string $type, ?string $format = null): bool
    {
        return $type == GrantInterface::CLASS;
    }

    /**
     * @param array<string, mixed> $data
     * @param array<mixed> $cntxt
     */
    public function denormalize($data, string $type, ?string $format = null, array $cntxt = []): GrantInterface
    {
        if (!$this->supportsDenormalization($data, $type, $format)) {
            throw new \InvalidArgumentException('Unable to denormalize, expecting GrantInterface');
        }

        $grant = new Claim(
            $this->denormalizeDate($data['claim_date'] ?? ''),
            $this->denormalizeAmount($data['claimed_amount'] ?? ''),
            $data['claim_description'] ?? ''
        );

        foreach ($data['grant_items'] as $grantData) {
            $grant = new Grant(
                $grant,
                $this->denormalizeAmount($grantData['granted_amount'] ?? ''),
                $grantData['grant_description'] ?? ''
            );
        }

        return $grant;
    }
}
