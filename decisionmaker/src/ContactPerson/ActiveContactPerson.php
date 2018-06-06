<?php

declare(strict_types = 1);

namespace asylgrp\decisionmaker\ContactPerson;

use byrokrat\banking\AccountNumber;

/**
 * An active contact person can receive payouts
 */
class ActiveContactPerson implements ContactPersonInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var AccountNumber
     */
    private $account;

    /**
     * @var string
     */
    private $mail;

    /**
     * @var string
     */
    private $phone;

    /**
     * @var string
     */
    private $comment;

    public function __construct(string $name, AccountNumber $account, string $mail, string $phone, string $comment)
    {
        $this->name = $name;
        $this->account = $account;
        $this->mail = $mail;
        $this->phone = $phone;
        $this->comment = $comment;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAccount(): AccountNumber
    {
        return $this->account;
    }

    public function getMail(): string
    {
        return $this->mail;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function getComment(): string
    {
        return $this->comment;
    }

    public function withMail(string $mail): ContactPersonInterface
    {
        return new static($this->name, $this->account, $mail, $this->phone, $this->comment);
    }

    public function withPhone(string $phone): ContactPersonInterface
    {
        return new static($this->name, $this->account, $this->mail, $phone, $this->comment);
    }

    public function withComment(string $comment): ContactPersonInterface
    {
        return new static($this->name, $this->account, $this->mail, $this->phone, $comment);
    }

    public function isActive(): bool
    {
        return !$this->isBlocked() && !$this->isBanned();
    }

    public function isBlocked(): bool
    {
        return false;
    }

    public function isBanned(): bool
    {
        return false;
    }

    public function activate(): ContactPersonInterface
    {
        return new ActiveContactPerson($this->name, $this->account, $this->mail, $this->phone, $this->comment);
    }

    public function block(): ContactPersonInterface
    {
        return new BlockedContactPerson($this->name, $this->account, $this->mail, $this->phone, $this->comment);
    }

    public function ban(): ContactPersonInterface
    {
        return new BannedContactPerson($this->name, $this->account, $this->mail, $this->phone, $this->comment);
    }
}
