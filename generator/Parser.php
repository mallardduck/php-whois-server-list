<?php

declare(strict_types=1);

namespace MallardDuck\WhoisDomainList\Generator;

use RuntimeException;

use function explode;
use function idn_to_ascii;
use function str_starts_with;
use function substr;
use function trim;

final class Parser
{
    protected string $body;

    /** @var array<TopLevelDomain> */
    protected array $domains = [];

    public function __construct(string $body)
    {
        $this->body = $body;
    }

    public function parse(): void
    {
        $lines = explode("\n", $this->body);

        foreach ($lines as $line) {
            $this->parseLine(trim($line));
        }
    }

    /**
     * @return TopLevelDomain[]
     */
    public function getTopLevelDomains(): array
    {
        return $this->domains;
    }

    protected function parseLine(string $line): void
    {
        if ($line === '') {
            return;
        }

        if (str_starts_with($line, '#') || str_starts_with($line, '//')) {
            return;
        }

        if (str_starts_with($line, '*.')) {
            $line = substr($line, 2);
        }

        $this->addTopLevelDomain($line);
    }

    protected function addTopLevelDomain(string $name): void
    {
        $asciiDomain = idn_to_ascii($name);
        if ($asciiDomain === false) {
            throw new RuntimeException('Cannot convert to IDN...' . $name);
        }
        $domain = new TopLevelDomain($asciiDomain);
        $this->domains[$name] = $domain;
    }
}
