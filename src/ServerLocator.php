<?php

declare(strict_types=1);

namespace MallardDuck\WhoisDomainList;

use JsonException;
use MallardDuck\WhoisDomainList\Exceptions\MissingArgument;
use MallardDuck\WhoisDomainList\Exceptions\UnknownTopLevelDomain;
use Safe\Exceptions\FilesystemException;
use Throwable;

use function Safe\file_get_contents;
use function json_decode;
use function strtolower;

use const JSON_THROW_ON_ERROR;

abstract class ServerLocator
{
    abstract public function getServerListPath(): string;

    /**
     * The metadata for the whois server list in use.
     *
     * @var array<string, mixed>
     */
    protected array $whoisServerListMetadata;

    /**
     * A collection of the TLDs and whois server list.
     *
     * @var array<string, string>
     */
    protected array $whoisServerCollection;

    /**
     * @throws FilesystemException
     * @throws JsonException
     */
    public function __construct()
    {
        $fileContents = file_get_contents($this->getServerListPath());

        /**
         * @var array{_meta: array<string, string>, data: array<string, string>} $parseResults
         */
        $parseResults = json_decode(
            $fileContents,
            true,
            512,
            JSON_THROW_ON_ERROR,
        );
        $this->whoisServerListMetadata = $parseResults['_meta'];
        $this->whoisServerCollection = $parseResults['data'];
    }

    /**
     * Finds and returns the last match looked up.
     *
     * @throws UnknownTopLevelDomain
     */
    protected function findWhoisServer(string $inputValue): string
    {
        try {
            return $this->whoisServerCollection[$inputValue];
        } catch (Throwable $throwable) { // @phpstan-ignore-line
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
