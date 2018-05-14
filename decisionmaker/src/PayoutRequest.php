<?php

declare(strict_types = 1);

namespace asylgrp\decisionmaker;

use asylgrp\decisionmaker\Grant\GrantInterface;

/**
 * A contact coupled with a granted payout
 */
class PayoutRequest
{
    /**
     * @var Contact
     */
    private $contact;

    /**
     * @var GrantInterface
     */
    private $grant;

    public function __construct(Contact $contact, GrantInterface $grant)
    {
        $this->contact = $contact;
        $this->grant = $grant;
    }

    public function getContact(): Contact
    {
        return $this->contact;
    }

    public function getGrant(): GrantInterface
    {
        return $this->grant;
    }

    public function withGrant(GrantInterface $newGrant): PayoutRequest
    {
        return new self($this->getContact(), $newGrant);
    }
}
