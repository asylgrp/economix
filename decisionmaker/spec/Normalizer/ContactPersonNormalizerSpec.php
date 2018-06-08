<?php

declare(strict_types = 1);

namespace decisionmaker\spec\asylgrp\decisionmaker\Normalizer;

use asylgrp\decisionmaker\Normalizer\ContactPersonNormalizer;
use asylgrp\decisionmaker\ContactPerson\ContactPersonInterface;
use asylgrp\decisionmaker\ContactPerson\ActiveContactPerson;
use asylgrp\decisionmaker\ContactPerson\BlockedContactPerson;
use asylgrp\decisionmaker\ContactPerson\BannedContactPerson;
use byrokrat\banking\AccountFactoryInterface;
use byrokrat\banking\AccountNumber;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ContactPersonNormalizerSpec extends ObjectBehavior
{
    function let(AccountFactoryInterface $accountFactory, ContactPersonInterface $contact, AccountNumber $account)
    {
        $contact->getName()->willReturn('name');
        $contact->getAccount()->willReturn($account);
        $account->getNumber()->willReturn('account');
        $contact->getMail()->willReturn('mail');
        $contact->getPhone()->willReturn('phone');
        $contact->getComment()->willReturn('comment');
        $contact->isActive()->willReturn(false);
        $contact->isBlocked()->willReturn(false);
        $contact->isBanned()->willReturn(false);

        $this->beConstructedWith($accountFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ContactPersonNormalizer::CLASS);
    }

    function it_fails_normalizing_not_supported_objects()
    {
        $this->shouldThrow(\InvalidArgumentException::CLASS)->during('normalize', [(object)[]]);
    }

    function it_can_normalize_active($contact)
    {
        $contact->isActive()->willReturn(true);
        $this->normalize($contact)->shouldBeLike([
            'name' => 'name',
            'account' => 'account',
            'mail' => 'mail',
            'phone' => 'phone',
            'comment' => 'comment',
            'status' => 'ACTIVE',
        ]);
    }

    function it_can_normalize_blocked($contact)
    {
        $contact->isBlocked()->willReturn(true);
        $this->normalize($contact)->shouldBeLike([
            'name' => 'name',
            'account' => 'account',
            'mail' => 'mail',
            'phone' => 'phone',
            'comment' => 'comment',
            'status' => 'BLOCKED',
        ]);
    }

    function it_can_normalize_banned($contact)
    {
        $contact->isBanned()->willReturn(true);
        $this->normalize($contact)->shouldBeLike([
            'name' => 'name',
            'account' => 'account',
            'mail' => 'mail',
            'phone' => 'phone',
            'comment' => 'comment',
            'status' => 'BANNED',
        ]);
    }

    function it_fails_normalize_on_inconsistent_status($contact)
    {
        $this->shouldThrow(\RuntimeException::CLASS)->duringNormalize($contact);
    }

    function it_fails_denormalizing_not_supported_objects()
    {
        $this->shouldThrow(\InvalidArgumentException::CLASS)->during('normalize', [null, 'not-contact']);
    }

    function it_can_denormalize_active($accountFactory, AccountNumber $account)
    {
        $data = [
            'name' => 'name',
            'account' => 'account',
            'mail' => 'mail',
            'phone' => 'phone',
            'comment' => 'comment',
            'status' => 'ACTIVE',
        ];

        $accountFactory->createAccount('account')->willReturn($account);
        $this->denormalize($data, ContactPersonInterface::CLASS)->shouldBeLike(
            new ActiveContactPerson('name', $account->getWrappedObject(), 'mail', 'phone', 'comment')
        );
    }

    function it_can_denormalize_blocked($accountFactory, AccountNumber $account)
    {
        $data = [
            'name' => 'name',
            'account' => 'account',
            'mail' => 'mail',
            'phone' => 'phone',
            'comment' => 'comment',
            'status' => 'BLOCKED',
        ];

        $accountFactory->createAccount('account')->willReturn($account);
        $this->denormalize($data, ContactPersonInterface::CLASS)->shouldBeLike(
            new BlockedContactPerson('name', $account->getWrappedObject(), 'mail', 'phone', 'comment')
        );
    }

    function it_can_denormalize_banned($accountFactory, AccountNumber $account)
    {
        $data = [
            'name' => 'name',
            'account' => 'account',
            'mail' => 'mail',
            'phone' => 'phone',
            'comment' => 'comment',
            'status' => 'BANNED',
        ];

        $accountFactory->createAccount('account')->willReturn($account);
        $this->denormalize($data, ContactPersonInterface::CLASS)->shouldBeLike(
            new BannedContactPerson('name', $account->getWrappedObject(), 'mail', 'phone', 'comment')
        );
    }

    function it_fails_denormalize_on_invalid_status()
    {
        $data = [
            'status' => 'NOT-A-VALID_STATUS',
        ];

        $this->shouldThrow(\RuntimeException::CLASS)->duringDenormalize($data, ContactPersonInterface::CLASS);
    }
}
