<?php

declare(strict_types=1);

use MallardDuck\Test\WhoisDomainList\MockLocators\CorruptJsonFileTestLocator;
use MallardDuck\Test\WhoisDomainList\MockLocators\EmptyFileTestLocator;
use MallardDuck\Test\WhoisDomainList\MockLocators\InvalidFileTestLocator;
use MallardDuck\WhoisDomainList\Exceptions\MissingArgument;
use MallardDuck\WhoisDomainList\Exceptions\UnknownTopLevelDomain;
use MallardDuck\WhoisDomainList\IanaServerLocator;
use MallardDuck\WhoisDomainList\PslServerLocator;

it('will throw an exception with URL provided as path', function () {
    if (!is_dir(PROJ_PARENT_TMP)) {
        mkdir(PROJ_PARENT_TMP);
    }
    if (!is_file(PROJ_PARENT_TMP . '/empty.json')) {
        copy(__DIR__ . '/stubs/empty.json', PROJ_PARENT_TMP . '/empty.json');
    }

    ini_set('open_basedir', dirname(__DIR__));
    $this->expectException(JsonException::class);
    $this->expectExceptionCode(0);
    $this->expectExceptionMessageMatches(
        '#Cannot get source file from path: ([\\\\\/a-zA-Z\-\:]+)' . DIRECTORY_SEPARATOR .
        'tests' . DIRECTORY_SEPARATOR . 'MockLocators' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR .
        '..' . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . 'empty.json#',
    );

    new InvalidFileTestLocator();
});

it('will throw an exception with empty file provided as path', function () {
    $this->expectException(JsonException::class);
    new EmptyFileTestLocator();
});

it('will throw an exception with corrupt JSON file provided as path', function () {
    $this->expectException(JsonException::class);
    new CorruptJsonFileTestLocator();
});


it('will throw an exception for empty input using IANA', function () {
    $ianaLocator = new IanaServerLocator();
    expect($ianaLocator)->toBeObject()->toBeInstanceOf(IanaServerLocator::class);
    $this->expectException(MissingArgument::class);
    $ianaLocator->getWhoisServer('');
});

it('will throw an exception for a fake TLD using IANA', function () {
    $ianaLocator = new IanaServerLocator();
    expect($ianaLocator)->toBeObject()->toBeInstanceOf(IanaServerLocator::class);
    $this->expectException(UnknownTopLevelDomain::class);
    $ianaLocator->getWhoisServer('bebop');
});

it('will throw an exception for empty input using PSL', function () {
    $pslLocator = new PslServerLocator();
    expect($pslLocator)->toBeObject()->toBeInstanceOf(PslServerLocator::class);
    $this->expectException(MissingArgument::class);
    $pslLocator->getWhoisServer('');
});

it('will throw an exception for a fake TLD using PSL', function () {
    $pslLocator = new PslServerLocator();
    expect($pslLocator)->toBeObject()->toBeInstanceOf(PslServerLocator::class);
    $this->expectException(UnknownTopLevelDomain::class);
    $pslLocator->getWhoisServer('bebop');
});
