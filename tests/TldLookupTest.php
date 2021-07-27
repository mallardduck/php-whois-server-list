<?php

declare(strict_types=1);

use MallardDuck\WhoisDomainList\IanaServerLocator;
use MallardDuck\WhoisDomainList\PslServerLocator;

$ianaLocator = new IanaServerLocator();
$pslLocator = new PslServerLocator();

it('can find the expected whois server using IANA', function ($domain, $expected) use ($ianaLocator) {
    expect($ianaLocator)->toBeObject()->toBeInstanceOf(IanaServerLocator::class);
    expect($ianaLocator->getWhoisServer($domain))->toBeString()->toBe($expected);
})->with([
    ['aaa', 'whois.iana.org'],
    ['ac', 'whois.nic.ac'],
    ['com', 'whois.verisign-grs.com'],
    ['me', 'whois.nic.me'],
    ['page', 'whois.nic.google'],
    ['xn--Vermgensberater-Ctb', 'whois.nic.xn--vermgensberater-ctb'],
    ['yt', 'whois.nic.yt'],
    ['youtube', 'whois.nic.google'],
    ['zero', 'whois.iana.org'],
    ['xn--p1acf', 'whois.nic.xn--p1acf'],
]);

it('can find the expected whois server using PSL', function ($domain, $expected) use ($pslLocator) {
    expect($pslLocator)->toBeObject()->toBeInstanceOf(PslServerLocator::class);
    expect($pslLocator->getWhoisServer($domain))->toBeString()->toBe($expected);
})->with([
    ['aaa', 'whois.iana.org'],
    ['ac', 'whois.nic.ac'],
    ['com', 'whois.verisign-grs.com'],
    ['me', 'whois.nic.me'],
    ['page', 'whois.nic.google'],
    ['aero', 'whois.aero'],
    ['passenger-association.aero', 'whois.aero'],
    ['xn--Vermgensberater-Ctb', 'whois.nic.xn--vermgensberater-ctb'],
    ['yt', 'whois.nic.yt'],
    ['youtube', 'whois.nic.google'],
    ['zero', 'whois.iana.org'],
    ['xn--p1acf', 'whois.nic.xn--p1acf'],
]);
