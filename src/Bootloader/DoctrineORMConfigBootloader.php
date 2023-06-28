<?php

declare(strict_types=1);

namespace Idiosyncratic\SpiralDoctrineBridge\Bootloader;

use Idiosyncratic\SpiralDoctrineBridge\DoctrineORMConfig;
use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Boot\DirectoriesInterface;
use Spiral\Boot\EnvironmentInterface;
use Spiral\Config\ConfigManager;
use Spiral\Config\Patch\Append;
use Spiral\Config\Patch\Set;
use Spiral\Core\Container;

final class DoctrineORMConfigBootloader extends Bootloader
{
    public function __construct(
        private readonly ConfigManager $configurator,
    ) {
    }

    public function init(
        EnvironmentInterface $env,
        DirectoriesInterface $directories,
    ) : void {
        if (! $directories->has('database')) {
            $directories->set('database', $directories->get('app') . 'database');
        }

        if (! $directories->has('doctrine')) {
            $directories->set('doctrine', $directories->get('database') . 'doctrine');
        }

        if (! $directories->has('doctrine-metadata')) {
            $directories->set('doctrine-metadata', $directories->get('doctrine') . 'metadata');
        }

        $this->configurator->setDefaults(DoctrineORMConfig::CONFIG, [
            'driver' => '',
            'port' => 0,
            'host' => '',
            'dbname' => '',
            'user' => '',
            'password' => '',
            'charset' => 'utf8',
            'metadata_driver' => 'xml',
            'metadata_paths' => [],
        ]);

        $this->addMetadataPath($directories->get('doctrine-metadata'));
    }

    public function boot(
        Container $container,
        DoctrineORMConfig $config,
    ) : void {
    }

    public function addMetadataPath(
        string $metadataPath,
    ) : void {
        $this->configurator->modify(
            DoctrineORMConfig::CONFIG,
            new Append('metadata_paths', null, $metadataPath),
        );
    }

    public function setCharset(
        string $charset,
    ) : void {
        $this->configurator->modify(
            DoctrineORMConfig::CONFIG,
            new Set('charset', $charset),
        );
    }

    public function setMetadataDriver(
        string $metadataDriver,
    ) : void {
        $this->configurator->modify(
            DoctrineORMConfig::CONFIG,
            new Set('metadata_driver', $metadataDriver),
        );
    }

    public function setDriver(
        int $driver,
    ) : void {
        $this->configurator->modify(
            DoctrineORMConfig::CONFIG,
            new Set('driver', $driver),
        );
    }

    public function setPort(
        int $port,
    ) : void {
        $this->configurator->modify(
            DoctrineORMConfig::CONFIG,
            new Set('port', $port),
        );
    }

    public function setHost(
        string $host,
    ) : void {
        $this->configurator->modify(
            DoctrineORMConfig::CONFIG,
            new Set('host', $host),
        );
    }

    public function setDbname(
        string $dbname,
    ) : void {
        $this->configurator->modify(
            DoctrineORMConfig::CONFIG,
            new Set('dbname', $dbname),
        );
    }

    public function setUser(
        string $user,
    ) : void {
        $this->configurator->modify(
            DoctrineORMConfig::CONFIG,
            new Set('user', $user),
        );
    }

    public function setPassword(
        string $password,
    ) : void {
        $this->configurator->modify(
            DoctrineORMConfig::CONFIG,
            new Set('password', $password),
        );
    }
}
