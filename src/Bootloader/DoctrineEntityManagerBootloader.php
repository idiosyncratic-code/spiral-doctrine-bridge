<?php

declare(strict_types=1);

namespace Idiosyncratic\SpiralDoctrineBridge\Bootloader;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMSetup;
use Idiosyncratic\SpiralDoctrineBridge\DoctrineORMConfig;
use Ramsey\Uuid\Doctrine\UuidType;
use RuntimeException;
use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Boot\EnvironmentInterface;
use Spiral\Cache\CacheStorageProviderInterface;
use Spiral\Core\BinderInterface;
use Symfony\Component\Cache\Adapter\Psr16Adapter;

final class DoctrineEntityManagerBootloader extends Bootloader
{
    protected const DEPENDENCIES = [
        DoctrineORMConfigBootloader::class,
    ];

    public function boot(
        BinderInterface $binder,
    ) : void {
        $binder->bindSingleton(
            EntityManagerInterface::class,
            static function (
                EnvironmentInterface $env,
                CacheStorageProviderInterface $cacheProvider,
                DoctrineORMConfig $config,
            ) {
                $devMode = (bool) $env->get('DEBUG');

                $dbParams = [
                    'driver' => $config->getDriver(),
                    'host' => $config->getHost(),
                    'user' => $config->getUser(),
                    'password' => $config->getPassword(),
                    'dbname' => $config->getDbname(),
                    'charset' => $config->getCharset(),
                ];

                if ($config->getCache() !== null && $devMode === false) {
                    $cache = new Psr16Adapter($cacheProvider->storage($config->getCache()));
                } else {
                    $cache = null;
                }

                switch ($config->getMetadataDriver()) {
                    case 'xml':
                        $doctrineConfig = ORMSetup::createXMLMetadataConfiguration(
                            $config->getMetadataPaths(),
                            $devMode,
                            null,
                            $cache,
                        );
                        break;
                    case 'yaml':
                        $doctrineConfig = ORMSetup::createYAMLMetadataConfiguration(
                            $config->getMetadataPaths(),
                            $devMode,
                            null,
                            $cache,
                        );
                        break;
                    default:
                        throw new RuntimeException('Unknown Doctrine Metadata driver');
                }

                 // @phpstan-ignore-next-line
                $connection = DriverManager::getConnection($dbParams, $doctrineConfig);

                $em = new EntityManager($connection, $doctrineConfig);

                $em->getConnection()
                   ->getDatabasePlatform()
                   ->registerDoctrineTypeMapping('enum', 'string');

                Type::AddType('uuid', UuidType::class);

                return $em;
            },
        );
    }
}
