<?php

namespace asylgrp\decisionmaker\ContactPerson;

use byrokrat\banking\AccountNumber;

interface ContactPersonInterface
{
    /**
     * Get full name
     */
    public function getName(): string;

    /**
     * Get account number
     */
    public function getAccount(): AccountNumber;

    /**
     * Get mail address
     */
    public function getMail(): string;

    /**
     * Get phone number
     */
    public function getPhone(): string;

    /**
     * Get free text comment
     */
    public function getComment(): string;

    /**
     * Create a new contact person object based on this one with a new mail
     */
    public function withMail(string $mail): ContactPersonInterface;

    /**
     * Create a new contact person object based on this one with a new phone
     */
    public function withPhone(string $phone): ContactPersonInterface;

    /**
     * Create a new contact person object based on this one with a new comment
     */
    public function withComment(string $comment): ContactPersonInterface;

    /**
     * Check if contact person is active (payouts are allowed)
     */
    public function isActive(): bool;

    /**
     * Check if contact person if blocked (payouts temporarily not allowed)
     */
    public function isBlocked(): bool;

    /**
     * Check if contact person is banned (payouts never expected to be allowed)
     */
    public function isBanned(): bool;

    /**
     * Create a new contact person object based on this one that is active
     */
    public function activate(): ContactPersonInterface;

    /**
     * Create a new contact person object based on this one that is blocked
     */
    public function block(): ContactPersonInterface;

    /**
     * Create a new contact person object based on this one that is banned
     */
    public function ban(): ContactPersonInterface;
}
