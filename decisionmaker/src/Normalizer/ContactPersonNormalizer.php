<?php

declare(strict_types = 1);

namespace asylgrp\decisionmaker\Normalizer;

use asylgrp\decisionmaker\ContactPerson\ContactPersonInterface;
use asylgrp\decisionmaker\ContactPerson\ActiveContactPerson;
use asylgrp\decisionmaker\ContactPerson\BlockedContactPerson;
use asylgrp\decisionmaker\ContactPerson\BannedContactPerson;
use byrokrat\banking\AccountFactoryInterface;
use byrokrat\banking\AccountFactory;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * Normalize contact person objects
 */
class ContactPersonNormalizer implements NormalizerInterface, DenormalizerInterface
{
    const STATUS_ACTIVE = 'ACTIVE';
    const STATUS_BLOCKED = 'BLOCKED';
    const STATUS_BANNED = 'BANNED';

    /**
     * @var AccountFactoryInterface
     */
    private $accountFactory;

    public function __construct(AccountFactoryInterface $accountFactory = null)
    {
        $this->accountFactory = $accountFactory ?: new AccountFactory;
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof ContactPersonInterface;
    }

    public function normalize($obj, $format = null, array $context = [])
    {
        if (!$this->supportsNormalization($obj, $format)) {
            throw new \InvalidArgumentException('Unable to normalize, expecting ContactPersonInterface');
        }

        return [
            'name' => $obj->getName(),
            'account' => $obj->getAccount()->getNumber(),
            'mail' => $obj->getMail(),
            'phone' => $obj->getPhone(),
            'comment' => $obj->getComment(),
            'status' => $this->normalizeStatus($obj),
        ];
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        return $type == ContactPersonInterface::CLASS;
    }

    public function denormalize($data, $type, $format = null, array $context = [])
    {
        if (!$this->supportsDenormalization($data, $type, $format)) {
            throw new \InvalidArgumentException('Unable to denormalize, expecting ContactPersonInterface');
        }

        $classname = $this->denormalizeStatus($data['status']);

        return new $classname(
            $data['name'],
            $this->accountFactory->createAccount($data['account']),
            $data['mail'],
            $data['phone'],
            $data['comment']
        );
    }

    private function normalizeStatus(ContactPersonInterface $contact): string
    {
        if ($contact->isActive()) {
            return self::STATUS_ACTIVE;
        }

        if ($contact->isBlocked()) {
            return self::STATUS_BLOCKED;
        }

        if ($contact->isBanned()) {
            return self::STATUS_BANNED;
        }

        throw new \RuntimeException('Unable to normalize contact person, unknown status.');
    }

    private function denormalizeStatus(string $normalizedStatus): string
    {
        if ($normalizedStatus == self::STATUS_ACTIVE) {
            return ActiveContactPerson::CLASS;
        }

        if ($normalizedStatus == self::STATUS_BLOCKED) {
            return BlockedContactPerson::CLASS;
        }

        if ($normalizedStatus == self::STATUS_BANNED) {
            return BannedContactPerson::CLASS;
        }

        throw new \RuntimeException("Unable to denormalize contact person, unknown status $normalizedStatus");
    }
}
