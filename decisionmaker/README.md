# decisionmaker

Create and manage payout decisions.

## Contact persons

Contact person objects carry name, account, mail, phone and comment, and comes
in three flawors:

1. `ActiveContactPerson` which can channel payouts
1. `BlockedContactPerson` which is temporarily blocked
1. `BannedContactPerson` which is never expected to channel payouts again

<!-- @example contactPerson -->
```php
use asylgrp\decisionmaker\ContactPerson\ActiveContactPerson;

$contactPerson = new ActiveContactPerson(
    'id',
    'name',
    (new \byrokrat\banking\AccountFactory)->createAccount('3300,180708-1235'),
    'mail',
    'phone',
    'comment'
);
```

## Requesting payouts

Generate fresh requests (claims) using the `PayoutRequestFactory`.

<!-- @example payout -->
<!-- @include contactPerson -->
```php
use asylgrp\decisionmaker\PayoutRequestFactory;
use Lcobucci\Clock\SystemClock;
use Money\Money;

$payout = (new PayoutRequestFactory(new SystemClock))->requestPayout($contactPerson, Money::SEK('5000'), 'description');
```

## Allocating

Allocation comes in four flawors.

1. `LazyFixed` allocates the same amount to all requests based on availiable funds.
   A max amount per request may be set.
1. `LazyRatio` allocates availiable funds based on claim amounts. The higher
   the claim the higher the grant..
1. `StaticFixed` allocates the same amount to all requests.
1. `StaticRatio` allocates based on a predefinied ratio.

Create complex allocator combinations using the `AllocatorBuilder`.

<!-- @example allocator -->
<!-- @include payout -->
```php
use asylgrp\decisionmaker\Allocator\AllocatorBuilder;

$allocator = (new AllocatorBuilder)
    ->addLazyFixed($max = Money::SEK('1000'))
    ->addLazyRatio()
    ->addStaticFixed($fixed = Money::SEK('10'))
    ->addStaticRatio($ratio = 0.5)
    ->getAllocator();
```

## Making decisions

<!-- @example decision -->
<!-- @include allocator -->
```php
use asylgrp\decisionmaker\DecisionMaker;

$fundsToGrant = Money::SEK('1000');

$decision = (new DecisionMaker($allocator, new SystemClock))->createDecision($fundsToGrant, [$payout], 'signature');
```

## Serializing

Decisions are serializable using the symfony serializer component.

> Note that to serialize decision objects you only need the DecisionNormalizer.
> The example below shows a more competent serializer that is able to serialize
> individual contacts and payout requests as well.

<!-- @example serializer -->
<!-- @include decision -->
```php
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

use asylgrp\decisionmaker\Normalizer\ContactPersonNormalizer;
use asylgrp\decisionmaker\Normalizer\DecisionNormalizer;
use asylgrp\decisionmaker\Normalizer\PayoutRequestNormalizer;

$serializer = new Serializer(
    [new ContactPersonNormalizer, new DecisionNormalizer, new PayoutRequestNormalizer],
    [new JsonEncoder]
);

$serialized = $serializer->serialize($decision, 'json', ['json_encode_options' => JSON_PRETTY_PRINT]);
```

<!--
@example validateSerialized
@include serializer
@expectOutput "/^\{.+\}$/s"
```php
echo $serialized;
```
-->

Deserializing also works as expected.

<!-- @example deserializer -->
<!-- @include serializer -->
```php
use asylgrp\decisionmaker\Decision;

$decision = $serializer->deserialize($serialized, Decision::CLASS, 'json');
```
