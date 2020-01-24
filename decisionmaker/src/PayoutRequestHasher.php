<?php

declare(strict_types = 1);

namespace asylgrp\decisionmaker;

/**
 * Hash collections of payout requests
 */
class PayoutRequestHasher
{
    public function hash(PayoutRequestCollection $payouts): string
    {
        $data = '';

        foreach ($payouts as $payout) {
            $data .= $payout->getContactPerson()->getName()
                . $payout->getContactPerson()->getAccount()->getNumber()
                . $payout->getGrant()->getClaimDate()->format('Ymd')
                . $payout->getGrant()->getClaimedAmount()->getAmount()
                . $payout->getGrant()->getGrantedAmount()->getAmount();
        }

        return md5($data);
    }
}
