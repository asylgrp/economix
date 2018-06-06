# decisionmaker

Create and manage payout decisions.

## TODO

### Allocation

<!-- @ignore -->
```php
namespace Allocator;

// Allokera samma summa till varje requets (baserat på tillgängliga medel och optional max value..)
// TODO Detta är färdigskriver

$alloc = new LazyAllocator(new FixedGranterFactory);
$payouts = $alloc->allocate($money, $payouts);

// Men hur gör en då för att använda pre-set guarantee osv..
// TODO Detta är färdigskriver

$alloc = new StaticAllocator(new FixedGranter(new SEK('1000')));

// Det krävs någon form av builder så att detta blir enkelt att styra från ini..
// TODO AllocatorBuilder är inte skriven..

$allocator = (new AllocatorBuilder)
    ->addLazyFixed($max = new SEK('1000'))
    ->addLazyRatio()
    ->addStaticFixed($fixed)
    ->addStaticRatio($ratio)
    ->getAllocator();
```

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

<!-- @example ContactPerson -->
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

<!-- @example PayoutRequest -->
<!-- @include ContactPerson -->
```php
use asylgrp\decisionmaker\PayoutRequestFactory;
use byrokrat\amount\Currency\SEK;

$payout = (new PayoutRequestFactory)->requestPayout($contactPerson, new SEK('100'), 'description');
```

## Serializing

Decisions are serializable using the symfony serializer component.

> Note that to serialize decision objects you only need the DecisionNormalizer.
> The example below shows a more competent serializer that is able to serialize
> individual contacts and payout requests as well.

<!-- @example serializer -->
<!-- @include PayoutRequest -->
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
