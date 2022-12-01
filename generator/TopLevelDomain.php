<?php

declare(strict_types=1);

namespace MallardDuck\WhoisDomainList\Generator;

use ArrayAccess;
use JsonSerializable;
use MallardDuck\Whois\Client;
use RuntimeException;

use function Safe\preg_match;
use function array_key_exists;
use function count;
use function sprintf;
use function strtolower;

/**
 * @template-implements ArrayAccess<string, string>
 */
final class TopLevelDomain implements ArrayAccess, JsonSerializable
{
    private const IANA_WHOIS = 'whois.iana.org';
    protected bool $whoisServerFound = false;
    protected string $name;
    protected string $server;

    public function __construct(
        string $name,
        ?string $server = null,
    ) {
        $this->name = strtolower($name);
        if ($server === null) {
            $this->server = $this::IANA_WHOIS;
        } else {
            $this->server = $server;
            $this->whoisServerFound = true;
        }
    }

    public function findAuthoritativeWhoisServer(): void
    {
        $client = new Client(self::IANA_WHOIS);
        $response = $client->makeRequest($this->name);
        if ($response === '') {
            throw new RuntimeException(sprintf('Empty DNS response for TLD `%s`, pause and try again.', $this->name));
        }

        $this->whoisServerFound = true;
        $matches = [];
        preg_match('/whois:(\s*)(.*)/i', $response, $matches);
        if (!isset($matches) || count($matches) === 0) {
            return;
        }

        $this->server = $matches[2];
    }

    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->toArray());
    }

    public function offsetGet($offset): string
    {
        if (!$this->offsetExists($offset)) {
            throw new RuntimeException('Property does not exist.');
        }

        return $this->{$offset};
    }

    public function offsetSet($offset, $value): void
    {
        $this->$offset = $value;
    }

    public function offsetUnset($offset): void
    {
        // Intentionally unimplemented
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getServer(): string
    {
        return $this->server;
    }

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        return [
            $this->name => $this->server,
        ];
    }

    /**
     * @return array<string, string>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
