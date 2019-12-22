<?php

declare(strict_types = 1);

namespace asylgrp\decisionmaker\ContactPerson;

use byrokrat\banking\AccountNumber;

abstract class AbstractContactPerson implements ContactPersonInterface
{
    private string $id;
    private string $name;
    private AccountNumber $account;
    private string $mail;
    private string $phone;
    private string $comment;

    final public function __construct(
        string $id,
        string $name,
        AccountNumber $account,
        string $mail,
        string $phone,
        string $comment
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->account = $account;
        $this->mail = $mail;
        $this->phone = $phone;
        $this->comment = $comment;
    }

    public function getId(): string
    {
        return $this->id;
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
        return new static($this->id, $this->name, $this->account, $mail, $this->phone, $this->comment);
    }

    public function withPhone(string $phone): ContactPersonInterface
    {
        return new static($this->id, $this->name, $this->account, $this->mail, $phone, $this->comment);
    }

    public function withComment(string $comment): ContactPersonInterface
    {
        return new static($this->id, $this->name, $this->account, $this->mail, $this->phone, $comment);
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
        return new ActiveContactPerson(
            $this->id,
            $this->name,
            $this->account,
            $this->mail,
            $this->phone,
            $this->comment
        );
    }

    public function block(): ContactPersonInterface
    {
        return new BlockedContactPerson(
            $this->id,
            $this->name,
            $this->account,
            $this->mail,
            $this->phone,
            $this->comment
        );
    }

    public function ban(): ContactPersonInterface
    {
        return new BannedContactPerson(
            $this->id,
            $this->name,
            $this->account,
            $this->mail,
            $this->phone,
            $this->comment
        );
    }
}
