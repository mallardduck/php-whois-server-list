<?php

declare(strict_types=1);

namespace MallardDuck\WhoisDomainList\Generator;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use RuntimeException;
use Symfony\Component\Console\SingleCommandApplication;

use function collect;
use function date;
use function file_put_contents;
use function json_encode;
use function sprintf;
use function strtolower;
use function time;

class GenerateCommandApplication extends SingleCommandApplication
{
    public int $now;

    public string $ianaTldDomainsUrl = 'https://data.iana.org/TLD/tlds-alpha-by-domain.txt';

    public string $publicSuffixListUrl = 'https://publicsuffix.org/list/public_suffix_list.dat';

    /**
     * @var array<array-key, TopLevelDomain>
     */
    public array $parsedDomainList = [];

    public function __construct(string $name = 'Whois TLD Server List Generator')
    {
        $this->now = time();
        parent::__construct($name);
    }

    /**
     * @throws GuzzleException
     */
    public function retrieveRemoteFile(string $url): string
    {
        $client = new Client();
        $response = $client->get($url);

        if ($response->getStatusCode() !== 200) {
            throw new RuntimeException('unable to load ' . $url);
        }

        return $response->getBody()->getContents();
    }

    private function getTempDirPath(): string
    {
        return __DIR__ . '/../build/tmp/';
    }

    public function parseResponse(string $body): void
    {
        file_put_contents($this->getTempDirPath() . date('Y_m_d-H_i_s', $this->now) . '_response.txt', $body);
        $parser = new Parser($body);
        $parser->parse();
        $this->parsedDomainList = $parser->getTopLevelDomains();
        file_put_contents(
            $this->getTempDirPath() . date('Y_m_d-H_i_s', $this->now) . 'domains.json',
            json_encode($this->parsedDomainList),
        );
    }

    /**
     * @param array<string, string>|null $meta
     */
    private function parsedListToJson(?array $meta = null): string
    {
        $flapMap = collect($this->parsedDomainList)
            ->flatMap(
                /**
                 * @return array<string, string>
                 */
                fn (TopLevelDomain $item) => $item->toArray(),
            );

        if ($meta !== null) {
            $flapMap->merge(['_meta' => $meta]);
        }

        return $flapMap
            ->toJson();
    }

    public function writeList(string $type, string $url): void
    {
        $tmpOut = sprintf('%s%s-%s.json', $this->getTempDirPath(), date('Y_m_d-H_i_s', $this->now), strtolower($type));
        file_put_contents($tmpOut, $this->parsedListToJson());
        $finalPath = sprintf('%s/../resources/%s-servers.json', __DIR__, strtolower($type));
        file_put_contents($finalPath, $this->parsedListToJson([
            'listType' => $type,
            'source' => $url,
        ]));
    }
}
