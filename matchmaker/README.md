# matchmaker

Match payouts with receipts.

The entry point for this package is the [`MatchMaker`](src/MatchMaker.php),
loaded with a set of [`matchers`](src/Matcher). Matchers are able to group 1 or
more [`matchables`](src/Matchable) together as [`matches`](src/Match). The
matchmaker creates a [`MatchCollection`](src/Match/MatchCollectionInterface.php).

## Matchability

This package ships with a simple [`Matchable`](src/Matchable/Matchable.php). You
may also supply your own [`MatchableInterface`](src/Matchable/MatchableInterface.php)
implementation.

> :information_source: Note that the order of matchables matters. Sort items
> oldest first if matching older payouts should be a priority.

## Balanceability

As definied in the [`MatchInterface`](src/Match/MatchInterface.php) matches
can be either successful (the amounts of all matched items balance out) or
failures (balance is non-zero). In some cases however you want a non-balanced
match to be reported as a success (a receipt of 99 kr matched to a payout of
100). Enter balanceability. A balanceable match is a match that can be amended
with one additional item to balance an acceptable diff. Balanceable matches are
thus always successful, as they are either balanced from the start, or can
programmatically be made to balance. Non-balanceable matches can, well, not be
balanced in this way, and are only reported as successful if balanced as is.

The inspector method `isBalanceable()` can be used to check if the item can be
balanced in it's current state (the method will only report true if item is
currently not balanced).

Generating balancing items is out of the scope of this package. But please see
the `getSuccessful()`, `getFailures()` and `getBalanceables()` methods of the
[`MatchCollectionInterface`](src/Match/MatchCollectionInterface.php) class.

## Generating matchables from accounting data

Load accounting data in the SIE format using [`byrokrat/accounting`](https://github.com/byrokrat/accounting).

<!-- @ignore -->
```php
$sieParser = (new \byrokrat\accounting\Sie4\Parser\Sie4ParserFactory)->createParser();
$accounting = $sieParser->parse(file_get_contents('verifications.se'));
```

<!--
@example hiddenAccountingData
@ignore
```php
namespace asylgrp\matchmaker;
$sieParser = (new \byrokrat\accounting\Sie4\Parser\Sie4ParserFactory)->createParser();
$accounting = $sieParser->parse("
    #FLAGGA 1
    #KONTO 1920 Bank
    #KONTO 1501 Name
    #KONTO 4000 Receipt
    #IB 0 1501 100.00
    #VER \"\" 1 20180830 \"description\"
    {
        #TRANS  1920 {} -200.00
        #TRANS  1501 {} 200.00
    }
    #VER \"\" 2 20180830 \"description\"
    {
        #TRANS  4000 {} 100.00
        #TRANS  1501 {} -100.00
    }
");
```
-->

Transform accounting data into matchables using the
[`AccountingMatchableFactory`](src/AccountingMatchableFactory.php).

> :information_source: Pass current year (year of accounting) at construct to
> enable the correct dates to be generated.

Generate matchables for all transactions to a specified account (including the
incoming balance) using the `createMatchablesForAccount()` method.

> :information_source: If an incoming balance is present a matchable with id `0`
> will be created. Use `rel:0` when entering receipts into bookkeeping that
> concerns a previous year for automatic matching.

> :information_source: Parsing verification descriptions internaly depends on
> the `descparser` package.

<!-- @example AccountingMatchableFactory -->
<!-- @include hiddenAccountingData -->
```php
$factory = AccountingMatchableFactory::createFactoryForYear(2017);

$matchables = $factory->createMatchablesForAccount(
    $accounting->select()->getAccount('1501'),
    $accounting
);
```

<!--
@example hiddenAccountingMatchableFactoryAssertion
@include AccountingMatchableFactory
```php
assert(count($matchables) == 3);
```
--->

## Matching individual matchables

<!--
@exampleContext
@example dump_matches()
@ignore
```php
namespace asylgrp\matchmaker\Matcher;

use asylgrp\matchmaker\MatchMaker;
use asylgrp\matchmaker\Matchable\Matchable;
use Money\Money;

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

The most basic matcher is the [`SingleMatcher`](src/Matcher/SingleMatcher.php)
which works by matching each matchable with nothing more then itself.

> :information_source: Terminate your list of matchers with a `SingleMatcher`
> to make sure non-matched items are reported as failures.

<!-- @example SingleMatcher -->
<!-- @expectOutput /^1,2$/ -->
```php
$matchMaker = new MatchMaker(new SingleMatcher);

$matches = $matchMaker->match(
    new Matchable('1', 'desc', new \DateTimeImmutable, Money::SEK('100')),
    new Matchable('2', 'desc', new \DateTimeImmutable, Money::SEK('-100'))
);

// 1,2
dump_matches($matches);
```

## Matching on date and amount

Use [`DateAndAmountMatcher`](src/Matcher/DateAndAmountMatcher.php) to match
pairs of matchables based on date and amount. You may specify the allowed
deviations in days and percentages.

> :information_source: By default `DateAndAmountMatcher` creates balanceable
> matches. Override by passing a `MatchFactoryInterface`
> implementation at construct. For more information see *Balanceability*.

<!-- @example DateAndAmountMatcher -->
<!-- @expectOutput /^1-2,3,4$/ -->
```php
$matchMaker = new MatchMaker(
    new DateAndAmountMatcher(new DateComparator(6), new AmountComparator(0.05)),
    new SingleMatcher
);

$matches = $matchMaker->match(
    new Matchable('1', '', new \DateTimeImmutable('2018-04-30'), Money::SEK('-100')),
    new Matchable('2', '', new \DateTimeImmutable('2018-05-03'), Money::SEK('100')),
    new Matchable('3', '', new \DateTimeImmutable('2018-06-29'), Money::SEK('100')),
    new Matchable('4', '', new \DateTimeImmutable('2018-07-29'), Money::SEK('-100'))
);

// 1-2,3,4
dump_matches($matches);
```

## Matching on predefined relations

The [`RelatedMatcher`](src/Matcher/RelatedMatcher.php) matches items marked as
related. Note below how `1-2-3` are matched as the relation is made explicit in
`3`, but that `6` is not connected to `4` and `5`.

> :information_source: Start your list of matchers with a `RelatedMatcher`
> to make sure explicit relations are honored.

<!-- @example RelatedMatcher -->
<!-- @expectOutput /^3-1-2,4-5,6$/ -->
```php
$matchMaker = new MatchMaker(
    new RelatedMatcher,
    new DateAndAmountMatcher(new DateComparator(6), new AmountComparator(0.05)),
    new SingleMatcher
);

$matches = $matchMaker->match(
    new Matchable('1', '', new \DateTimeImmutable('2018-04-30'), Money::SEK('100')),
    new Matchable('2', '', new \DateTimeImmutable('2018-05-03'), Money::SEK('-99')),
    new Matchable('3', '', new \DateTimeImmutable('2018-06-29'), Money::SEK('-1'), ['1', '2']),
    new Matchable('4', '', new \DateTimeImmutable('2018-04-30'), Money::SEK('100')),
    new Matchable('5', '', new \DateTimeImmutable('2018-05-03'), Money::SEK('-99')),
    new Matchable('6', '', new \DateTimeImmutable('2018-06-29'), Money::SEK('-1'))
);

// 3-1-2,4-5,6
dump_matches($matches);
```

## Matching zero amount items

In some configurations items with zero amount might be included in matches
where it is not desirable. Use the [`ZeroAmountMatcher`](src/Matcher/ZeroAmountMatcher.php)
to match these items as single matches.

> :information_source: It is a good idea to add a `ZeroAmountMatcher` directly
> after the `RelatedMatcher` when creating your matchmaker. That makes it
> possible to specify relations for deleted (zeroed) items and in this way
> report identified duplicates.

<!-- @example ZeroAmountMatcher -->
<!-- @expectOutput /^1$/ -->
```php
$matchMaker = new MatchMaker(new ZeroAmountMatcher);

$matches = $matchMaker->match(
    new Matchable('1', '', new \DateTimeImmutable, Money::SEK('0'))
);

// 1
dump_matches($matches);
```

## Matching groups

Match groups in one-to-many style using the [`GroupingMatcher`](src/Matcher/GroupingMatcher.php).

<!-- @example GroupingMatcher -->
<!-- @expectOutput /^1-4-3-2,5$/ -->
```php
$matchMaker = new MatchMaker(
    new GroupingMatcher(new DateComparator(12), new AmountComparator(0.05)),
    new SingleMatcher
);

$matches = $matchMaker->match(
    new Matchable('1', '', new \DateTimeImmutable('2018-04-28'), Money::SEK('75')),
    new Matchable('2', '', new \DateTimeImmutable('2018-05-03'), Money::SEK('-25')),
    new Matchable('3', '', new \DateTimeImmutable('2018-05-03'), Money::SEK('-25')),
    new Matchable('4', '', new \DateTimeImmutable('2018-05-03'), Money::SEK('-25')),
    new Matchable('5', '', new \DateTimeImmutable('2018-05-03'), Money::SEK('-25'))
);

// 1-4-3-2,5
dump_matches($matches);
```

## Matching on amount alone

Use [`AmountMatcher`](src/Matcher/AmountMatcher.php) to match on amount alone.
It is equivalent to using a `DateAndAmountMatcher` with the date max deviation
set to `365`.

<!-- @example AmountMatcher -->
<!-- @expectOutput /^1-2$/ -->
```php
$matchMaker = new MatchMaker(
    new AmountMatcher(new AmountComparator(0.05)),
    new SingleMatcher
);

$matches = $matchMaker->match(
    new Matchable('1', '', new \DateTimeImmutable('2018-05-30'), Money::SEK('100')),
    new Matchable('2', '', new \DateTimeImmutable('2018-06-15'), Money::SEK('-100'))
);

// 1-2
dump_matches($matches);
```

## Matching on date alone

Use [`DateMatcher`](src/Matcher/DateMatcher.php) to match on date alone. It is
equivalent to using a `DateAndAmountMatcher` with the amount max deviation set
to `1.0`.

> :information_source: Note that `DateMatcher` creates non-balanceable matches.
> A match on date alone is really uncertain, and should only be viewed as a
> partial match. For more information see *Balanceability*.

<!-- @example DateMatcher -->
<!-- @expectOutput /^1-2$/ -->
```php
$matchMaker = new MatchMaker(
    new DateMatcher(new DateComparator(6)),
    new SingleMatcher
);

$matches = $matchMaker->match(
    new Matchable('1', '', new \DateTimeImmutable('2018-05-30'), Money::SEK('100')),
    new Matchable('2', '', new \DateTimeImmutable('2018-06-05'), Money::SEK('-50'))
);

// 1-2
dump_matches($matches);
```

## Putting it all together...

<!-- @example "fullstack-example" -->
<!-- @expectOutput /^2-1,3-4,5-6,7-9-8,10-11,12-13,14$/ -->
```php
$max10days = new DateComparator(10);
$max0percent = new AmountComparator(0.0);
$max5percent = new AmountComparator(0.05);

$matchMaker = new MatchMaker(
    new RelatedMatcher,
    new ZeroAmountMatcher,
    new DateAndAmountMatcher($max10days, $max0percent),
    new DateAndAmountMatcher($max10days, $max5percent),
    new GroupingMatcher($max10days, $max0percent),
    new GroupingMatcher($max10days, $max5percent),
    new AmountMatcher($max5percent),
    new DateMatcher($max10days),
    new SingleMatcher
);

$matches = $matchMaker->match(
    // matched by relation
    new Matchable('1', '', new \DateTimeImmutable('2018-05-30'), Money::SEK('100')),
    new Matchable('2', '', new \DateTimeImmutable('2018-06-05'), Money::SEK('-75'), ['1']),

    // matched by date and perfect amount
    new Matchable('3', '', new \DateTimeImmutable('2018-06-05'), Money::SEK('100')),
    new Matchable('4', '', new \DateTimeImmutable('2018-06-05'), Money::SEK('-100')),

    // matched by date and amount
    new Matchable('5', '', new \DateTimeImmutable('2018-06-05'), Money::SEK('100')),
    new Matchable('6', '', new \DateTimeImmutable('2018-06-05'), Money::SEK('-98')),

    //matched as group
    new Matchable('7', '', new \DateTimeImmutable('2018-06-05'), Money::SEK('100')),
    new Matchable('8', '', new \DateTimeImmutable('2018-06-05'), Money::SEK('-50')),
    new Matchable('9', '', new \DateTimeImmutable('2018-06-05'), Money::SEK('-49')),

    // matched even though date is off
    new Matchable('10', '', new \DateTimeImmutable('2018-06-05'), Money::SEK('100')),
    new Matchable('11', '', new \DateTimeImmutable('2018-08-05'), Money::SEK('-98')),

    // partial match
    new Matchable('12', '', new \DateTimeImmutable('2018-09-05'), Money::SEK('300')),
    new Matchable('13', '', new \DateTimeImmutable('2018-09-05'), Money::SEK('-200')),

    // no match
    new Matchable('14', '', new \DateTimeImmutable('2018-10-05'), Money::SEK('100'))
);

// 2-1,3-4,5-6,7-9-8,10-11,12-13,14
dump_matches($matches);
```

## Filtering collections of matches

Matches can be inspected using filters. The predefined set of filters include:

* `UnaccountedPreviousYearFilter` for finding unmatched amounts from precious
  year (id: `0`).
* `UnaccountedDateFilter` for finding unmatched amounts older than a date limit.
* `UnaccountedAmountFilter` for finding collections where the total unmatched
  amount is greater than amount limit.
* `LogicalOrFilter` for combining multiple filters.

A complex filter that returns sucess if there is an unmatched amount from
previous year or there is an unmatched amount older than *20181016* or the total
unmatched amount is greater than *10 000 SEK*:

<!-- @example "filter" -->
```php
use asylgrp\matchmaker\Filter\LogicalOrFilter;
use asylgrp\matchmaker\Filter\UnaccountedPreviousYearFilter;
use asylgrp\matchmaker\Filter\UnaccountedDateFilter;
use asylgrp\matchmaker\Filter\UnaccountedAmountFilter;

$filter = new LogicalOrFilter(
    new UnaccountedPreviousYearFilter,
    new UnaccountedDateFilter(new \DateTimeImmutable('20181016')),
    new UnaccountedAmountFilter(Money::SEK('10000'))
);
```

Evaluating a filter returns a [`ResultInterface`](src/Filter/ResultInterface.php)
object.

<!-- @ignore -->
```php
$result = $filter->evaluate($matches);

if ($result->isSuccess()) {
    // perform some action..
}
```
