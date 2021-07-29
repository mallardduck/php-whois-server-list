<?php

declare(strict_types=1);

use MallardDuck\WhoisDomainList\IanaServerLocator;
use MallardDuck\WhoisDomainList\PslServerLocator;

$ianaLocator = new IanaServerLocator();
$pslLocator = new PslServerLocator();

it('can find the expected whois server using IANA', function () use ($ianaLocator) {
    expect($ianaLocator)
        ->toBeObject()
        ->toBeInstanceOf(IanaServerLocator::class);
    expect($ianaLocator->getServerListPath())->toBeString()->toEndWith('/../resources/iana-servers.json');
});

it('has proper value set for collection using IANA', function () use ($ianaLocator) {
    expect($ianaLocator)
        ->toBeObject()
        ->toBeInstanceOf(IanaServerLocator::class)
        ->toHaveProperty('whoisServerCollection');
    expect(getProperty($ianaLocator, 'whoisServerCollection'))->toBeArray()->toHaveKey('_meta');
});

it('can find the expected whois server using PSL', function () use ($pslLocator) {
    expect($pslLocator)
        ->toBeObject()
        ->toBeInstanceOf(PslServerLocator::class);
    expect($pslLocator->getServerListPath())->toBeString()->toEndWith('/../resources/psl-servers.json');
});

it('has proper value set for collection using PSL', function () use ($pslLocator) {
    expect($pslLocator)
        ->toBeObject()
        ->toBeInstanceOf(PslServerLocator::class)
        ->toHaveProperty('whoisServerCollection');
    expect(getProperty($pslLocator, 'whoisServerCollection'))->toBeArray()->toHaveKey('_meta');
});
