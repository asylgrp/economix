<?php

declare(strict_types = 1);

namespace asylgrp\decisionmaker;

use asylgrp\decisionmaker\ContactPerson\ContactPersonInterface;
use asylgrp\decisionmaker\Grant\GrantInterface;

/**
 * A contact coupled with a granted payout
 */
class PayoutRequest
{
    private ContactPersonInterface $contact;
    private GrantInterface $grant;

    public function __construct(ContactPersonInterface $contact, GrantInterface $grant)
    {
        $this->contact = $contact;
        $this->grant = $grant;
    }

    public function getContactPerson(): ContactPersonInterface
    {
        return $this->contact;
    }

    public function getGrant(): GrantInterface
    {
        return $this->grant;
    }

    public function withGrant(GrantInterface $newGrant): PayoutRequest
    {
        return new self($this->getContactPerson(), $newGrant);
    }
}
