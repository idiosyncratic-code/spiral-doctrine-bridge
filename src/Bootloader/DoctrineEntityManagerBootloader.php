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
use Spiral\Core\BinderInterface;

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
                DoctrineORMConfig $doctrineConfig,
            ) {
                $dbParams = [
                    'driver' => $doctrineConfig->getDriver(),
                    'host' => $doctrineConfig->getHost(),
                    'user' => $doctrineConfig->getUser(),
                    'password' => $doctrineConfig->getPassword(),
                    'dbname' => $doctrineConfig->getDbname(),
                    'charset' => $doctrineConfig->getCharset(),
                ];

                switch ($doctrineConfig->getMetadataDriver()) {
                    case 'xml':
                        $doctrineConfig = ORMSetup::createXMLMetadataConfiguration(
                            $doctrineConfig->getMetadataPaths(),
                            (bool) $env->get('DEBUG'),
                        );
                        break;
                    case 'yaml':
                        $doctrineConfig = ORMSetup::createYAMLMetadataConfiguration(
                            $doctrineConfig->getMetadataPaths(),
                            (bool) $env->get('DEBUG'),
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
