<?php

declare(strict_types=1);

use MallardDuck\WhoisDomainList\Ipv4ServerLocator;

$ipv4Locator = new Ipv4ServerLocator();

it('can find an IPs whois server', function () use ($ipv4Locator) {
    expect($ipv4Locator)->toBeObject()->toBeInstanceOf(Ipv4ServerLocator::class);
    $ipv4Locator->getWhoisServer('10.2.1.100');
});
