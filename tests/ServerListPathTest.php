<?php

declare(strict_types=1);

use MallardDuck\WhoisDomainList\IanaServerLocator;
use MallardDuck\WhoisDomainList\Ipv4ServerLocator;
use MallardDuck\WhoisDomainList\PslServerLocator;

$ianaLocator = new IanaServerLocator();
$pslLocator = new PslServerLocator();

it('can find the expected whois server list using IANA', function () use ($ianaLocator) {
    expect($ianaLocator)
        ->toBeObject()
        ->toBeInstanceOf(IanaServerLocator::class);
    expect($ianaLocator->getServerListPath())
        ->toBeString()
        ->toEndWith('resources' . DIRECTORY_SEPARATOR . 'iana-servers.json');
});

it('has proper value set for collection using IANA', function () use ($ianaLocator) {
    expect($ianaLocator)
        ->toBeObject()
        ->toBeInstanceOf(IanaServerLocator::class)
        ->toHaveProperty('whoisServerListMetadata')
        ->toHaveProperty('whoisServerCollection');
    expect(getProperty($ianaLocator, 'whoisServerCollection'))->toBeArray()->toHaveKey('com');
});

it('can find the expected whois server list using PSL', function () use ($pslLocator) {
    expect($pslLocator)
        ->toBeObject()
        ->toBeInstanceOf(PslServerLocator::class);
    expect($pslLocator->getServerListPath())
        ->toBeString()
        ->toEndWith('resources' . DIRECTORY_SEPARATOR . 'psl-servers.json');
});

it('has proper value set for collection using PSL', function () use ($pslLocator) {
    expect($pslLocator)
        ->toBeObject()
        ->toBeInstanceOf(PslServerLocator::class)
        ->toHaveProperty('whoisServerListMetadata')
        ->toHaveProperty('whoisServerCollection');
    expect(getProperty($pslLocator, 'whoisServerCollection'))->toBeArray()->toHaveKey('co.uk');
});

it('can find the expected whois server list for IPv4', function () {
    $ipv4Locator = new Ipv4ServerLocator();
    expect($ipv4Locator)
        ->toBeObject()
        ->toBeInstanceOf(Ipv4ServerLocator::class);
    expect($ipv4Locator->getServerListPath())
        ->toBeString()
        ->toEndWith('resources' . DIRECTORY_SEPARATOR . 'ipv4-servers.json');
});
