<?php

declare(strict_types = 1);

namespace asylgrp\decisionmaker\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use asylgrp\decisionmaker\Contact;

/**
 * Normalize contact person objects
 */
class ContactNormalizer implements NormalizerInterface, DenormalizerInterface
{
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Contact;
    }

    public function normalize($obj, $format = null, array $context = [])
    {
        if (!$this->supportsNormalization($obj, $format)) {
            throw new \InvalidArgumentException('Unable to normalize, expecting Contact');
        }

        return [
        ];
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        return $type == Contact::CLASS;
    }

    public function denormalize($data, $type, $format = null, $context = [])
    {
        if (!$this->supportsDenormalization($data, $type, $format)) {
            throw new \InvalidArgumentException('Unable to denormalize, expecting Contact');
        }

        return new Contact;
    }
}
