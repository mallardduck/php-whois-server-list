<?php

declare(strict_types=1);

use MallardDuck\WhoisDomainList\Helper;

it('verifies the helper exists', function () {
    expect('MallardDuck\WhoisDomainList\Helper::ipMatchCidr')->toBeCallable();
});

it('verifies the helper works with odd input', function ($ipAddress, $subnet, $expected) {
    expect(Helper::ipMatchCidr($ipAddress, $subnet))->toBeBool()->toBe($expected);
})->with([
    ['192.168.1.1', '192.168.0.0', false],
    ['192.168.1.1', '192.168.1.14', false],
    ['192.168.1.1', '192.168.1.1', true],
    ['192.168.1.127', '192.168.1.128/30', false],
    ['192.168.1.132', '192.168.1.128/30', false],
    ['192.168.1.130', '192.168.1.128/30', true],
    ['192.168.1.131', '192.168.1.128/30', true],
]);

it('verifies the helper works', function ($ipAddress, $subnet, $expected) {
    expect(Helper::ipMatchCidr($ipAddress, $subnet))->toBeBool()->toBe($expected);
})->with([
    ['192.168.1.1', '192.168.0.0/16', true],
    ['192.168.1.1', '192.168.2.42/16', true],
    ['192.168.1.1', '192.168.8.0/24', false],
    ['196.168.1.1', '192.168.8.0/8', false],
    ['10.0.0.1', '192.168.0.0/16', false],
    ['10.0.0.1', '10.12.0.0/16', false],
    ['10.12.0.1', '10.12.0.0/16', true],
    ['68.49.128.204', '68.32.0.0/11', true],
]);
