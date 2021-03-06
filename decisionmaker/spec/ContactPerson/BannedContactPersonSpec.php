<?php

declare(strict_types = 1);

namespace decisionmaker\spec\asylgrp\decisionmaker\ContactPerson;

use asylgrp\decisionmaker\ContactPerson\ActiveContactPerson;
use asylgrp\decisionmaker\ContactPerson\BlockedContactPerson;
use asylgrp\decisionmaker\ContactPerson\BannedContactPerson;
use asylgrp\decisionmaker\ContactPerson\ContactPersonInterface;
use byrokrat\banking\AccountNumber;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class BannedContactPersonSpec extends ObjectBehavior
{
    function let(AccountNumber $account)
    {
        $this->beConstructedWith('', '', $account, '', '', '');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(BannedContactPerson::CLASS);
    }

    function it_implements_interface()
    {
        $this->shouldHaveType(ContactPersonInterface::CLASS);
    }

    function it_should_not_be_active()
    {
        $this->shouldNotBeActive();
    }

    function it_should_not_be_blocked()
    {
        $this->shouldNotBeBlocked();
    }

    function it_should_be_banned()
    {
        $this->shouldBeBanned();
    }

    function it_contains_an_id($account)
    {
        $this->beConstructedWith('id', '', $account, '', '', '');
        $this->getId()->shouldReturn('id');
    }

    function it_can_be_created_from_id()
    {
        $this->beConstructedThrough(function () {
            return BannedContactPerson::fromId('foobar');
        });
        $this->getId()->shouldReturn('foobar');
        $this->shouldHaveType(BannedContactPerson::CLASS);
    }

    function it_contains_a_name($account)
    {
        $this->beConstructedWith('', 'name', $account, '', '', '');
        $this->getName()->shouldReturn('name');
    }

    function it_contains_an_account($account)
    {
        $this->getAccount()->shouldReturn($account);
    }

    function it_contains_a_mail($account)
    {
        $this->beConstructedWith('', '', $account, 'mail', '', '');
        $this->getMail()->shouldReturn('mail');
    }

    function it_contains_a_phone($account)
    {
        $this->beConstructedWith('', '', $account, '', 'phone', '');
        $this->getPhone()->shouldReturn('phone');
    }

    function it_contains_a_comment($account)
    {
        $this->beConstructedWith('', '', $account, '', '', 'comment');
        $this->getComment()->shouldReturn('comment');
    }

    function it_can_create_with_name($account)
    {
        $this->beConstructedWith('', 'name', $account, '', '', '');
        $this->withName('foobar')->shouldBeLike(
            new BannedContactPerson('', 'foobar', $account->getWrappedObject(), '', '', '')
        );
    }

    function it_can_create_with_account($account, AccountNumber $newAccount)
    {
        $this->beConstructedWith('', '', $account, '', '', '');
        $this->withAccount($newAccount)->shouldBeLike(
            new BannedContactPerson('', '', $newAccount->getWrappedObject(), '', '', '')
        );
    }

    function it_can_create_with_mail($account)
    {
        $this->beConstructedWith('', '', $account, 'mail', '', '');
        $this->withMail('foobar')->shouldBeLike(
            new BannedContactPerson('', '', $account->getWrappedObject(), 'foobar', '', '')
        );
    }

    function it_can_create_with_phone($account)
    {
        $this->beConstructedWith('', '', $account, '', 'phone', '');
        $this->withPhone('foobar')->shouldBeLike(
            new BannedContactPerson('', '', $account->getWrappedObject(), '', 'foobar', '')
        );
    }

    function it_can_create_with_comment($account)
    {
        $this->beConstructedWith('', '', $account, '', '', 'comment');
        $this->withComment('foobar')->shouldBeLike(
            new BannedContactPerson('', '', $account->getWrappedObject(), '', '', 'foobar')
        );
    }

    function it_can_activate($account)
    {
        $this->beConstructedWith('id', 'name', $account, 'mail', 'phone', 'comment');
        $this->activate()->shouldBeLike(
            new ActiveContactPerson('id', 'name', $account->getWrappedObject(), 'mail', 'phone', 'comment')
        );
    }

    function it_can_block($account)
    {
        $this->beConstructedWith('id', 'name', $account, 'mail', 'phone', 'comment');
        $this->block()->shouldBeLike(
            new BlockedContactPerson('id', 'name', $account->getWrappedObject(), 'mail', 'phone', 'comment')
        );
    }

    function it_can_ban($account)
    {
        $this->beConstructedWith('id', 'name', $account, 'mail', 'phone', 'comment');
        $this->ban()->shouldBeLike(
            new BannedContactPerson('id', 'name', $account->getWrappedObject(), 'mail', 'phone', 'comment')
        );
    }
}
