<?php

declare(strict_types=1);

namespace App\Infrastructure\Annotations;

use TheCodingMachine\GraphQLite\Annotations\MiddlewareAnnotationInterface;

/**
 * @Annotation
 * @Target({"ANNOTATION", "METHOD"})
 */
class Admin implements MiddlewareAnnotationInterface
{
}
