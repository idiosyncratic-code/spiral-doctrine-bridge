<?php

declare(strict_types=1);

namespace Idiosyncratic\SpiralDoctrineBridge;

use Spiral\Core\InjectableConfig;

final class DoctrineORMConfig extends InjectableConfig
{
    public const CONFIG = 'doctrine-orm';

    /** @var array{
     *     'driver': string,
     *     'port': int,
     *     'host': string,
     *     'dbname': string,
     *     'user': string,
     *     'password': string,
     *     'charset': string,
     *     'metadata_driver': string,
     *     'metadata_paths': array<string>,
     *     'cache': ?string
     * }
     */
    protected array $config = [
        'driver' => '',
        'port' => 0,
        'host' => '',
        'dbname' => '',
        'user' => '',
        'password' => '',
        'charset' => '',
        'metadata_driver' => '',
        'metadata_paths' => [],
        'cache' => null,
    ];

    public function getDriver() : string
    {
        return $this->config['driver'];
    }

    public function getPort() : int
    {
        return $this->config['port'];
    }

    public function getHost() : string
    {
        return $this->config['host'];
    }

    public function getDbname() : string
    {
        return $this->config['dbname'];
    }

    public function getUser() : string
    {
        return $this->config['user'];
    }

    public function getPassword() : string
    {
        return $this->config['password'];
    }

    public function getCharset() : string
    {
        return $this->config['charset'];
    }

    public function getMetadataDriver() : string
    {
        return $this->config['metadata_driver'];
    }

    /** @return array<string> */
    public function getMetadataPaths() : array
    {
        return $this->config['metadata_paths'];
    }

    public function getCache() : ?string
    {
        return $this->config['cache'];
    }
}
