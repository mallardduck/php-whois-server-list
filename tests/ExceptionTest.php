<?php

declare(strict_types=1);

use MallardDuck\WhoisDomainList\Exceptions\MissingArgument;
use MallardDuck\WhoisDomainList\Exceptions\UnknownTopLevelDomain;
use MallardDuck\WhoisDomainList\IanaServerLocator;
use MallardDuck\WhoisDomainList\PslServerLocator;

$ianaLocator = new IanaServerLocator();
$pslLocator = new PslServerLocator();

it('will throw an exception for empty input using IANA', function () use ($ianaLocator) {
    expect($ianaLocator)->toBeObject()->toBeInstanceOf(IanaServerLocator::class);
    $this->expectException(MissingArgument::class);
    $ianaLocator->getWhoisServer('');
});

it('will throw an exception for a fake TLD using IANA', function () use ($ianaLocator) {
    expect($ianaLocator)->toBeObject()->toBeInstanceOf(IanaServerLocator::class);
    $this->expectException(UnknownTopLevelDomain::class);
    $ianaLocator->getWhoisServer('bebop');
});

it('will throw an exception for empty input using PSL', function () use ($pslLocator) {
    expect($pslLocator)->toBeObject()->toBeInstanceOf(PslServerLocator::class);
    $this->expectException(MissingArgument::class);
    $pslLocator->getWhoisServer('');
});

it('will throw an exception for a fake TLD using PSL', function () use ($pslLocator) {
    expect($pslLocator)->toBeObject()->toBeInstanceOf(PslServerLocator::class);
    $this->expectException(UnknownTopLevelDomain::class);
    $pslLocator->getWhoisServer('bebop');
});
