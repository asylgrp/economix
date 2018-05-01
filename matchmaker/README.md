# matchmaker

Match payouts with receipts.

## Matchability

This package ships with a simple `Matchable` implementation. In most cases
however you will be better of by supplying your own `MatchableInterface`
implementation.

> :warning: Please note that the order of the supplied matchables matters. For
> example make sure to sort items on date with the oldest first if matching
> older payouts should be a priority.

## Balanceability

TODO: beskriv vad det är och hur det kan användas...

<!--
@exampleContext
@ignore
```php
namespace asylgrp\matchmaker\Matcher;

use asylgrp\matchmaker\MatchMaker;
use asylgrp\matchmaker\Matchable\Matchable;
use byrokrat\amount\Amount;

function dump_matches($matchCollection)
{
    echo implode(
        ',',
        array_map(
            function ($match) {
                return implode(
                    '-',
                    array_map(
                        function ($matched) {
                            return $matched->getId();
                        },
                        $match->getMatched()
                    )
                );
            },
            $matchCollection->getMatches()
        )
    );
}
```
-->

## Matching individual matchables

The most basic matcher is the `SingleMatcher` which works by matching each
matchable with nothing more then itself.

> :information_source: Always terminate your list of matchers with a
> `SingleMatcher` to make sure that non-matched items are reported as failures!

<!-- @example SingleMatcher -->
<!-- @expectOutput /^1,2$/ -->
```php
$matchMaker = new MatchMaker(
    new SingleMatcher
);

$matches = $matchMaker->match(
    new Matchable('1', new \DateTimeImmutable('2018-04-30'), new Amount('100')),
    new Matchable('2', new \DateTimeImmutable('2018-05-03'), new Amount('-100'))
);

// 1,2
dump_matches($matches);
```

## Matching on date and amount

Use `DateAndAmountMatcher` to match pairs of matchables based on date and amount.
You may specify the allowed deviations in days and percentages.

<!-- @example DateAndAmountMatcher -->
<!-- @expectOutput /^1-2,3,4$/ -->
```php
$matchMaker = new MatchMaker(
    new DateAndAmountMatcher(new DateComparator(6), new AmountComparator(0.05)),
    new SingleMatcher
);

$matches = $matchMaker->match(
    new Matchable('1', new \DateTimeImmutable('2018-04-30'), new Amount('-100')),
    new Matchable('2', new \DateTimeImmutable('2018-05-03'), new Amount('100')),
    new Matchable('3', new \DateTimeImmutable('2018-06-29'), new Amount('100')),
    new Matchable('4', new \DateTimeImmutable('2018-07-29'), new Amount('-100'))
);

// 1-2,3,4
dump_matches($matches);
```

> :information_source: Note that by default `DateAndAmountMatcher` creates
> balanceable matches. Override by passing a `MatchFactoryInterface`
> implementation at construct. For more information see **Balanceability**.

## Matching on predefined relations

The `RelatedMatcher` matches items marked as related. Note below how `1-2-3` are
matched as the relation is made explicit in `3`, but that `6` is not connected
to `4` and `5`.

<!-- @example RelatedMatcher -->
<!-- @expectOutput /^3-1-2,4-5,6$/ -->
```php
$matchMaker = new MatchMaker(
    new RelatedMatcher,
    new DateAndAmountMatcher(new DateComparator(6), new AmountComparator(0.05)),
    new SingleMatcher
);

$matches = $matchMaker->match(
    new Matchable('1', new \DateTimeImmutable('2018-04-30'), new Amount('100')),
    new Matchable('2', new \DateTimeImmutable('2018-05-03'), new Amount('-99')),
    new Matchable('3', new \DateTimeImmutable('2018-06-29'), new Amount('-1'), ['1', '2']),
    new Matchable('4', new \DateTimeImmutable('2018-04-30'), new Amount('100')),
    new Matchable('5', new \DateTimeImmutable('2018-05-03'), new Amount('-99')),
    new Matchable('6', new \DateTimeImmutable('2018-06-29'), new Amount('-1'))
);

// 3-1-2,4-5,6
dump_matches($matches);
```
> :information_source: Always start your list of matchers with a `RelatedMatcher`
> to make sure explicit relations are honored.

## Matching on amount alone

Use `AmountMatcher` to match on amount alone. It is equivalent to using a
`DateAndAmountMatcher` with the date max deviation set to `365`.

<!-- @example AmountMatcher -->
<!-- @expectOutput /^1-2$/ -->
```php
$matchMaker = new MatchMaker(
    new AmountMatcher(new AmountComparator(0.05)),
    new SingleMatcher
);

$matches = $matchMaker->match(
    new Matchable('1', new \DateTimeImmutable('2018-05-30'), new Amount('100')),
    new Matchable('2', new \DateTimeImmutable('2018-06-15'), new Amount('-100'))
);

// 1-2
dump_matches($matches);
```

## Matching on date alone

Use `DateMatcher` to match on date alone. It is equivalent to using a
`DateAndAmountMatcher` with the amount max deviation set to `0.0`.

> :information_source: Note that `DateMatcher` creates non-balanceable matches.
> Matching on anly date is really uncertain, and should only be viewed as a
> partial match. For more information see **Balanceability**.

<!-- @example DateMatcher -->
<!-- @expectOutput /^1-2$/ -->
```php
$matchMaker = new MatchMaker(
    new DateMatcher(new DateComparator(6)),
    new SingleMatcher
);

$matches = $matchMaker->match(
    new Matchable('1', new \DateTimeImmutable('2018-05-30'), new Amount('100')),
    new Matchable('2', new \DateTimeImmutable('2018-06-05'), new Amount('-50'))
);

// 1-2
dump_matches($matches);
```

## Putting it all togheter...

<!-- @example "Putting it all togheter" -->
```php
$max6days = new DateComparator(6);
$max0percent = new AmountComparator(0.0);
$max5percent = new AmountComparator(0.05);

$matchMaker = new MatchMaker(
    new RelatedMatcher,
    new DateAndAmountMatcher($max6days, $max0percent),
    new DateAndAmountMatcher($max6days, $max5percent),
    #new GroupingMatcher($max6days, $max0percent),
    #new GroupingMatcher($max6days, $max5percent),
    new AmountMatcher($max5percent),
    new DateMatcher($max6days),
    new SingleMatcher
);

// TODO continue here...
```
