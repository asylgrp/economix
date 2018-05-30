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

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof GrantInterface;
    }

    public function normalize($grant, $format = null, array $context = [])
    {
        if (!$this->supportsNormalization($grant, $format)) {
            throw new \InvalidArgumentException('Unable to normalize, expecting GrantInterface');
        }

        $grantItems = [];

        foreach ($grant->getGrantItems() as $grantItem) {
            $grantItems[] = [
                'granted_amount' => $this->normalizeAmount($grantItem->getGrantedAmount()),
                'grant_description' => $grantItem->getGrantDescription()
            ];
        }

        return [
            'claim_date' => $this->normalizeDate($grant->getClaimDate()),
            'claimed_amount' => $this->normalizeAmount($grant->getClaimedAmount()),
            'claim_description' => $grant->getClaimDescription(),
            'grant_items' => $grantItems
        ];
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        return $type == GrantInterface::CLASS;
    }

    public function denormalize($data, $type, $format = null, array $context = [])
    {
        if (!$this->supportsDenormalization($data, $type, $format)) {
            throw new \InvalidArgumentException('Unable to denormalize, expecting GrantInterface');
        }

        $grant = new Claim(
            $this->denormalizeDate($data['claim_date']),
            $this->denormalizeAmount($data['claimed_amount']),
            $data['claim_description']
        );

        foreach ($data['grant_items'] as $grantData) {
            $grant = new Grant(
                $grant,
                $this->denormalizeAmount($grantData['granted_amount']),
                $grantData['grant_description']
            );
        }

        return $grant;
    }
}
