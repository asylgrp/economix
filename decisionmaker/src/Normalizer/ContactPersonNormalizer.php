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

    private AccountFactoryInterface $accountFactory;

    public function __construct(AccountFactoryInterface $accountFactory = null)
    {
        $this->accountFactory = $accountFactory ?: new AccountFactory;
    }

    /**
     * @param mixed $obj
     */
    public function supportsNormalization($obj, ?string $format = null): bool
    {
        return $obj instanceof ContactPersonInterface;
    }

    /**
     * @param mixed $obj
     * @param array<mixed> $cntxt
     * @return array<string, string>
     */
    public function normalize($obj, ?string $format = null, array $cntxt = []): array
    {
        if (!$this->supportsNormalization($obj, $format)) {
            throw new \InvalidArgumentException('Unable to normalize, expecting ContactPersonInterface');
        }

        /** @var ContactPersonInterface $obj */

        return [
            'id' => $obj->getId(),
            'name' => $obj->getName(),
            'account' => $obj->getAccount()->getNumber(),
            'mail' => $obj->getMail(),
            'phone' => $obj->getPhone(),
            'comment' => $obj->getComment(),
            'status' => $this->normalizeStatus($obj),
        ];
    }

    /**
     * @param array<string, string> $data
     */
    public function supportsDenormalization($data, string $type, ?string $format = null): bool
    {
        return $type == ContactPersonInterface::CLASS;
    }

    /**
     * @param array<string, string> $data
     * @param array<mixed> $cntxt
     */
    public function denormalize($data, string $type, ?string $format = null, array $cntxt = []): ContactPersonInterface
    {
        if (!$this->supportsDenormalization($data, $type, $format)) {
            throw new \InvalidArgumentException('Unable to denormalize, expecting ContactPersonInterface');
        }

        $classname = $this->denormalizeStatus($data['status']);

        return new $classname(
            $data['id'] ?? '',
            $data['name'] ?? '',
            $this->accountFactory->createAccount($data['account'] ?? ''),
            $data['mail'] ?? '',
            $data['phone'] ?? '',
            $data['comment'] ?? ''
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
