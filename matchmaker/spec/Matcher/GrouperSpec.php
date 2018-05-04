<?php

declare(strict_types = 1);

namespace matchmaker\spec\asylgrp\matchmaker\Matcher;

use asylgrp\matchmaker\Matcher\Grouper;
use asylgrp\matchmaker\Matcher\AmountComparator;
use asylgrp\matchmaker\Matcher\DateComparator;
use asylgrp\matchmaker\Matchable\MatchableInterface;
use asylgrp\matchmaker\Matchable\MatchableGroup;
use byrokrat\amount\Amount;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use PhpSpec\Exception\Example\FailureException;

class GrouperSpec extends ObjectBehavior
{
    function let(
        DateComparator $dateComp,
        AmountComparator $amountComp,
        MatchableInterface $matchableA,
        MatchableInterface $matchableB,
        MatchableInterface $matchableC
    ) {
        $dateComp->equals(Argument::cetera())->willReturn(true);
        $amountComp->equals(Argument::cetera())->willReturn(false);

        $this->beConstructedWith($dateComp, $amountComp, 2, 100);

        $matchableA->getDate()->willReturn(new \DateTimeImmutable);
        $matchableB->getDate()->willReturn(new \DateTimeImmutable);
        $matchableC->getDate()->willReturn(new \DateTimeImmutable);

        $matchableA->getAmount()->willReturn(new Amount('0'));
        $matchableB->getAmount()->willReturn(new Amount('0'));
        $matchableC->getAmount()->willReturn(new Amount('0'));

        $matchableA->getId()->willReturn('A');
        $matchableB->getId()->willReturn('B');
        $matchableC->getId()->willReturn('C');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Grouper::CLASS);
    }

    function it_can_find_groups($matchableA, $matchableB, $matchableC)
    {
        $this->findGroups([$matchableA, $matchableB, $matchableC])->shouldReturnGroups([
            [$matchableA, $matchableB],
            [$matchableA, $matchableC],
            [$matchableB, $matchableC],
            [$matchableA, $matchableB, $matchableC],
        ]);
    }

    function it_can_read_min_and_max_group_size($dateComp, $amountComp, $matchableA, $matchableB, $matchableC)
    {
        $this->beConstructedWith($dateComp, $amountComp, 1, 2);

        $this->findGroups([$matchableA, $matchableB, $matchableC])->shouldReturnGroups([
            [$matchableA],
            [$matchableB],
            [$matchableC],
            [$matchableA, $matchableB],
            [$matchableA, $matchableC],
            [$matchableB, $matchableC],
        ]);
    }

    function it_filters_on_amont($amountComp, $matchableA, $matchableB, $matchableC)
    {
        $matchableA->getAmount()->willReturn(new Amount('100'));
        $matchableB->getAmount()->willReturn(new Amount('100'));
        $matchableC->getAmount()->willReturn(new Amount('-100'));

        $amountComp->equals(new Amount('100'), new Amount('-100'))->willReturn(true);

        $this->findGroups([$matchableA, $matchableB, $matchableC])->shouldReturnGroups([
            [$matchableA, $matchableB],
        ]);
    }

    function it_filters_on_date($dateComp, $matchableA, $matchableB, $matchableC)
    {
        $matchableA->getDate()->willReturn(new \DateTimeImmutable('20180430'));
        $matchableB->getDate()->willReturn(new \DateTimeImmutable('20180430'));
        $matchableC->getDate()->willReturn(new \DateTimeImmutable('20180530'));

        $dateComp->equals(new \DateTimeImmutable('20180430'), new \DateTimeImmutable('20180530'))->willReturn(false);

        $this->findGroups([$matchableA, $matchableB, $matchableC])->shouldReturnGroups([
            [$matchableA, $matchableB],
        ]);
    }

    public function getMatchers(): array
    {
        return [
            'returnGroups' => function ($returned, $expected) {
                if (count($returned) != count($expected)) {
                    throw new FailureException(sprintf(
                        'Unexpected group count, found %s, expected %s',
                        count($returned),
                        count($expected)
                    ));
                }

                foreach ($expected as $index => $expectedGroup) {
                    foreach ($returned as $returnedGroup) {
                        $returnedGroup = $returnedGroup->getMatchables();
                        if (count($returnedGroup) != count($expectedGroup)) {
                            continue;
                        }

                        foreach ($returnedGroup as $returnedItem) {
                            if (!in_array($returnedItem, $expectedGroup, true)) {
                                continue 2;
                            }
                        }

                        continue 2;
                    }

                    throw new FailureException("Unable to find group $index");
                }

                return true;
            }
        ];
    }
}
