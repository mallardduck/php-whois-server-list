<?php

declare(strict_types=1);

namespace MallardDuck\WhoisDomainList;

use function count;
use function explode;
use function ip2long;

class Helper
{
    public static function ipMatchCidr(string $ip, string $range): bool
    {
        $rangeParts = explode('/', $range);
        if (count($rangeParts) === 1) {
            $subnet = $rangeParts[0];
            $bits = 32;
        } else {
            [$subnet, $bits] = $rangeParts;
            $bits = $bits === '' ? 32 : (int) $bits;
        }

        $ipLong = ip2long($ip);
        $subnetLong = ip2long($subnet);
        $mask = -1 << 32 - $bits;
        $subnetLong &= $mask;

        return ($ipLong & $mask) === $subnetLong;
    }
}
