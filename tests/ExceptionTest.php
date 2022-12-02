<?php

declare(strict_types=1);

use MallardDuck\Test\WhoisDomainList\MockLocators\CorruptJsonFileTestLocator;
use MallardDuck\Test\WhoisDomainList\MockLocators\EmptyFileTestLocator;
use MallardDuck\Test\WhoisDomainList\MockLocators\InvalidFileTestLocator;
use MallardDuck\Test\WhoisDomainList\MockLocators\UrlPathTestLocator;
use MallardDuck\WhoisDomainList\Exceptions\MissingArgument;
use MallardDuck\WhoisDomainList\Exceptions\UnknownTopLevelDomain;
use MallardDuck\WhoisDomainList\IanaServerLocator;
use MallardDuck\WhoisDomainList\PslServerLocator;
use Safe\Exceptions\FilesystemException;

it('throws an exception with invalid path', function () {
    if (!is_dir(PROJ_PARENT_TMP)) {
        mkdir(PROJ_PARENT_TMP);
    }
    if (!is_file(PROJ_PARENT_TMP . '/empty.json')) {
        copy(__DIR__ . '/stubs/empty.json', PROJ_PARENT_TMP . '/empty.json');
    }
    ini_set('open_basedir', dirname(__DIR__));
    $this->expectDeprecationMessageMatches(
        '#Cannot get source file from path: `([a-zA-Z\-\:\/\\\\]+)[\/\\\\]tmp[\/\\\\]empty.json`#',
    );

    new InvalidFileTestLocator();
})->throws(FilesystemException::class);

it('throws an exception with URL provided as path', function () {
    new UrlPathTestLocator();
})->throws(
    FilesystemException::class,
    'Cannot get source file from path: `https://api.plos.org/search?q=title:DNA`',
);

it('will throw an exception with empty file provided as path', function () {
    new EmptyFileTestLocator();
})->throws(JsonException::class);

it('will throw an exception with corrupt JSON file provided as path', function () {
    new CorruptJsonFileTestLocator();
})->throws(JsonException::class);

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
