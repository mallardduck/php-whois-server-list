<?php

declare(strict_types=1);

namespace MallardDuck\WhoisDomainList\Generator;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\SingleCommandApplication;

use function Safe\date;
use function Safe\file_get_contents;
use function Safe\file_put_contents;
use function Safe\json_encode;
use function array_keys;
use function array_map;
use function array_merge;
use function array_values;
use function count;
use function file_exists;
use function serialize;
use function sleep;
use function sprintf;
use function strtolower;
use function time;
use function unserialize;
use function usleep;

final class GenerateCommandApplication extends SingleCommandApplication
{
    private const TIMESTAMP_DOWN_TO_DAY = 'Y_m_d';
    private const TIMESTAMP_DOWN_TO_HOURS = 'Y_m_d-H';

    public int $now;

    public string $ianaTldDomainsUrl = 'https://data.iana.org/TLD/tlds-alpha-by-domain.txt';

    public string $publicSuffixListUrl = 'https://publicsuffix.org/list/public_suffix_list.dat';

    /**
     * @var array<array-key, TopLevelDomain>
     */
    public array $parsedDomainList = [];

    /** @psalm-suppress PropertyNotSetInConstructor */
    public OutputInterface $output;

    public function __construct(string $name = 'Whois TLD Server List Generator')
    {
        $this->now = time();
        parent::__construct($name);
    }

    public function handle(InputInterface $input, OutputInterface $output): int
    {
        $this->output = $output;
        $output->writeln('Generating package code...');

        $buildType = match ($input->getOption('build')) {
            'iana' => BuildMode::IANA,
            'psl' => BuildMode::PSL,
            default => BuildMode::BOTH,
        };

        if ($buildType === BuildMode::IANA || $buildType === BuildMode::BOTH) {
            $this->executeBuildStepsFor(BuildMode::IANA);
        }

        if ($buildType === BuildMode::PSL || $buildType === BuildMode::BOTH) {
            $this->executeBuildStepsFor(BuildMode::PSL);
        }

        $output->writeln('Done!');

        return 0;
    }

    private function executeBuildStepsFor(string $buildType): void
    {
        $this->output->writeln('Building ' . $buildType . ' list based TLD list...');
        $this->output->writeln('Load file...');
        $body = $this->getListBody($buildType);

        $this->output->writeln('Parse response...');
        $this->parseResponse($buildType, $body);
        $this->output->writeln('Discovering TLDs...');
        $this->discoverServersLoop($buildType);

        $this->output->writeln('Generate class...');
        $this->writeList($buildType);
    }

    private function getListBody(string $buildType): string
    {
        $responsePath = $this->getTempDirPath() . $buildType . '-'
            . date(self::TIMESTAMP_DOWN_TO_HOURS, $this->now) . '_response.txt';
        if (file_exists($responsePath)) {
            return file_get_contents($responsePath);
        }

        $responseBody = $this->retrieveRemoteFile($this->getListUrlByType($buildType));
        file_put_contents($responsePath, $responseBody);

        return $responseBody;
    }

    private function getTempDirPath(): string
    {
        return __DIR__ . '/../build/tmp/';
    }

    /**
     * @throws GuzzleException
     */
    private function retrieveRemoteFile(string $url): string
    {
        $client = new Client();
        $response = $client->get($url);

        if ($response->getStatusCode() !== 200) {
            throw new RuntimeException('unable to load ' . $url);
        }

        return $response->getBody()->getContents();
    }

    private function getListUrlByType(string $type): string
    {
        return match ($type) {
            BuildMode::IANA => $this->ianaTldDomainsUrl,
            BuildMode::PSL => $this->publicSuffixListUrl,
            default => throw new RuntimeException('Invalid type provided...')
        };
    }

    private function parseResponse(string $type, string $body): void
    {
        $parser = new Parser($body);
        $parser->parse();
        $this->parsedDomainList = $parser->getTopLevelDomains();
    }

    private function getParsedTldsTempPath(string $type): string
    {
        return $this->getTempDirPath() . $type . '-'
            . date(self::TIMESTAMP_DOWN_TO_DAY, $this->now) . '_domains.raw';
    }

    private function discoverServersLoop(string $type): void
    {
        $tmpPath = $this->getParsedTldsTempPath($type);
        $partialProgress = [];
        $saveCount = $completedItems = 0;
        $tldsListCount = count($this->parsedDomainList);
        $this->output->writeln('Will loop over #' . $tldsListCount . ' of TLDs.');

        // Do something to recover partial progress...
        if (file_exists($tmpPath)) {
            /**
             * @var TopLevelDomain[] $partialProgress
             */
            $partialProgress = unserialize(file_get_contents($tmpPath));
            $completedItems = count($partialProgress);
            $this->output->writeln('Skipping over ' . $completedItems . ' previously completed TLDs.');
            $this->output->writeln('Only need to do ' . ($tldsListCount - $completedItems) . ' TLDs.');
        }

        $arrayKeys = array_keys($this->parsedDomainList);
        do {
            $tldItem = $this->parsedDomainList[$arrayKeys[$completedItems]];
            $this->output->writeln('Looking up: ' . $tldItem->getName());
            try {
                $tldItem->findAuthoritativeWhoisServer();
            } catch (RuntimeException $e) {
                $this->output->writeln('Cannot get response from WHOIS server. Will sleep for a few seconds.');
                sleep(2);

                continue 1;
            }
            $partialProgress[$tldItem->getName()] = $tldItem;
            $saveCount++;
            $completedItems++;
            if ($saveCount > 4) {
                $this->output->writeln('Saving partial progress to prevent data loss...');
                file_put_contents(
                    $tmpPath,
                    serialize($partialProgress),
                );
                $saveCount = 0;
            } else {
                usleep(500000);
            }
        } while ($tldsListCount !== $completedItems);
        file_put_contents(
            $tmpPath,
            serialize($partialProgress),
        );
        $this->parsedDomainList = $partialProgress;
    }

    private function writeList(string $type): void
    {
        $finalPath = sprintf('%s/../resources/%s-servers.json', __DIR__, strtolower($type));
        file_put_contents($finalPath, $this->parsedListToJson([
            'listType' => $type,
            'source' => $this->getListUrlByType($type),
        ]));
    }

    /**
     * @param array<string, string> $meta
     */
    private function parsedListToJson(array $meta): string
    {
        // Apply an array map to render values as arrays...
        $items = array_map(fn ($value) => $value->toArray(), $this->parsedDomainList);
        $items = array_merge([], ...array_values($items));

        // Then prepare to render as json...
        $results = [
            '_meta' => $meta,
            'data' => $items,
        ];

        return json_encode($results);
    }
}
