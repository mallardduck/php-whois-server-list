<?php

declare(strict_types=1);

namespace MallardDuck\WhoisDomainList;

use function array_filter;
use function array_values;
use function dirname;
use function explode;
use function str_starts_with;

use const ARRAY_FILTER_USE_KEY;
use const DIRECTORY_SEPARATOR;

class Ipv4ServerLocator extends ServerLocator
{
    public function getServerListPath(): string
    {
        return dirname(__DIR__) . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'ipv4-servers.json';
    }

    /**
     * Finds and returns the last match looked up.
     */
    protected function findWhoisServer(string $inputValue): string
    {
        $firstOctet = explode('.', $inputValue)[0];

        $options = array_filter(
            $this->whoisServerCollection,
            static fn (string $key) => str_starts_with($key, $firstOctet . '.'),
            ARRAY_FILTER_USE_KEY,
        );
        $options = array_filter(
            $options,
            static fn (string $key) => Helper::ipMatchCidr($inputValue, $key),
            ARRAY_FILTER_USE_KEY,
        );

        return array_values($options)[0];
    }
}
