<?php

declare(strict_types=1);

namespace MallardDuck\WhoisDomainList;

use JsonException;
use MallardDuck\WhoisDomainList\Exceptions\MissingArgument;
use MallardDuck\WhoisDomainList\Exceptions\UnknownTopLevelDomain;
use Throwable;

use function assert;
use function file_get_contents;
use function is_string;
use function json_decode;
use function strtolower;

use const JSON_THROW_ON_ERROR;

abstract class ServerLocator
{
    abstract public function getServerListPath(): string;

    /**
     * A collection of the TLDs and whois server list.
     *
     * @var array<string, string>
     */
    protected array $whoisServerCollection;

    /**
     * @throws JsonException
     */
    public function __construct()
    {
        try {
            $fileContents = file_get_contents($this->getServerListPath());
            assert(is_string($fileContents));
        } catch (Throwable $throwable) {
            throw new JsonException('Cannot get source file from path: ' . $this->getServerListPath());
        }

        /**
         * @var array<string, string> $parseResults
         */
        $parseResults = json_decode(
            $fileContents,
            true,
            512,
            JSON_THROW_ON_ERROR,
        );
        $this->whoisServerCollection = $parseResults;
    }

    /**
     * Finds and returns the last match looked up.
     *
     * @throws UnknownTopLevelDomain
     */
    protected function findWhoisServer(string $inputValue): string
    {
        // This is actually faster than checking for a key to throw an exception.
        try {
            return $this->whoisServerCollection[$inputValue];
        } catch (Throwable $throwable) {
            throw UnknownTopLevelDomain::create($inputValue);
        }
    }

    /**
     * Get the Whois server of the domain provided, or previously found domain.
     *
     * @param string $inputValue The domain being looked up via whois.
     *
     * @return string Returns the domain name of the whois server.
     *
     * @throws MissingArgument
     * @throws UnknownTopLevelDomain
     */
    public function getWhoisServer(string $inputValue): string
    {
        if ($inputValue === '') {
            throw MissingArgument::create();
        }

        return $this->findWhoisServer(strtolower($inputValue));
    }
}
