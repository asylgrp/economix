# decisionmaker

Create and manage payout decisions.

## TODO

### DecisionMaker

En DecisionMaker som innehåller denna logic...

<!-- @ignore -->
```php
$alloc = (new AllocatorBuilder)->...->getAllocator();

$funds = new SEK('4500');

// TODO funkar det att göra id så? Kan ta hänsyn till claim dates osv...
// TODO hash måste beräknas efter allocation...
// TODO finns decisionmaker\Utils\SyStemClock att använda..

return new Decision(
    $payoutRequestHasher->getHash($payoutRequests),
    $clock->now(),
    $funds,
    $alloc->allocate($funds, $payoutRequests)
);
```

### Presentation

PDF-genererar logiken med sortering och gruppering(?)...

* Lägg till antal KP:s till sidhuvud i beslut.

## Handling contact persons

Contact person objects carry name, account, mail, phone and comment, and comes
in three flawors:

* ActiveContactPerson which can channel payouts
* BlockedContactPerson which is temporarily block
* BannedContactPerson which is never expected to channel payouts again

<!-- @example contactPerson -->
```php
use asylgrp\decisionmaker\ContactPerson\ActiveContactPerson;

$contactPerson = new ActiveContactPerson(
    'name',
    (new \byrokrat\banking\AccountFactory)->createAccount('1230'),
    'mail',
    'phone',
    'comment'
);
```

## Requesting payouts

Generate fresh requests (claims) using the PayoutRequestFactory.

<!-- @example payout -->
<!-- @include contactPerson -->
```php
use asylgrp\decisionmaker\PayoutRequestFactory;
use byrokrat\amount\Currency\SEK;

$payout = (new PayoutRequestFactory)->requestPayout($contactPerson, new SEK('100'), 'description');
```

## Allocating

Allocation comes in four flawors.

* `LazyFixed`: Allocate the same amount to all requests based on availiable funds.
  A max amount per request may be set.
* `LazyRatio`: Allocate availiable funds based on claim amounts. The higher
  the claim the higher the grant..
* `StaticFixed`: Allocate the same amount to all requests.
* `StaticRatio`: Allocate the to all requests based on a predefinied ratio.

Create complex allocator combinations using the `AllocatorBuilder`.

<!-- @example allocator -->
<!-- @include payout -->
```php
use asylgrp\decisionmaker\Allocator\AllocatorBuilder;

$allocator = (new AllocatorBuilder)
    ->addLazyFixed($max = new SEK('1000'))
    ->addLazyRatio()
    ->addStaticFixed($fixed = new SEK('10'))
    ->addStaticRatio($ratio = 0.5)
    ->getAllocator();
```

## Serializing

Decisions are serializable using the symfony serializer component.

> Note that to serialize decision objects you only need the DecisionNormalizer.
> The example below shows a more competent serializer that is able to serialize
> individual contacts and payout requests as well.

<!-- @example serializer -->
<!-- @include payout -->
<!-- @expectOutput "/^\{.+\}$/s" -->
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

// TODO use decisionbuilder here
$grant = new \asylgrp\decisionmaker\Grant\Claim(new \DateTimeImmutable, new \byrokrat\amount\Currency\SEK('100'), 'test');
$grant = new \asylgrp\decisionmaker\Grant\Grant($grant, new \byrokrat\amount\Currency\SEK('50'), 'granitng..');
$grant = new \asylgrp\decisionmaker\Grant\Grant($grant, new \byrokrat\amount\Currency\SEK('50'), 'granting again');

$payout = new \asylgrp\decisionmaker\PayoutRequest($contactPerson, $grant);

echo $serializer->serialize($payout, 'json', ['json_encode_options' => JSON_PRETTY_PRINT]);
```
