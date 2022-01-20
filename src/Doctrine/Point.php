<?php

declare(strict_types=1);

namespace TarfinLabs\LaravelSpatial\Doctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class Point extends Type
{
    const POINT = 'point';

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return self::POINT;
    }

    public function getName(): string
    {
        return self::POINT;
    }
}