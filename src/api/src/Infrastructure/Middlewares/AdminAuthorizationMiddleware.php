<?php

declare(strict_types=1);

namespace App\Infrastructure\Middlewares;

use App\Domain\Enum\UserTypeEnum;
use App\Domain\Repository\UserRepository;
use App\Infrastructure\Annotations\Admin;
use GraphQL\Type\Definition\FieldDefinition;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;
use TheCodingMachine\GraphQLite\Middlewares\FieldHandlerInterface;
use TheCodingMachine\GraphQLite\Middlewares\FieldMiddlewareInterface;
use TheCodingMachine\GraphQLite\QueryField;
use TheCodingMachine\GraphQLite\QueryFieldDescriptor;
use function assert;

/**
 * Middleware which implements the @Admin annotation for controllers
 */
class AdminAuthorizationMiddleware implements FieldMiddlewareInterface
{
    protected UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function process(QueryFieldDescriptor $queryFieldDescriptor, FieldHandlerInterface $fieldHandler): ?FieldDefinition
    {
        $annotations = $queryFieldDescriptor->getMiddlewareAnnotations();
        $admin = $annotations->getAnnotationByType(Admin::class);
        assert($admin === null || $admin instanceof Admin);
        if ($admin === null) {
            return $fieldHandler->handle($queryFieldDescriptor);
        }

        try {
            $user = $this->userRepository->getLoggedUser();
            if ($user->getType()->getId() !== UserTypeEnum::ADMINISTRATOR) {
                return QueryField::unauthorizedError($queryFieldDescriptor, false);
            }
        } catch (AuthenticationCredentialsNotFoundException $e) {
            return QueryField::unauthorizedError($queryFieldDescriptor, true);
        }

        return $fieldHandler->handle($queryFieldDescriptor);
    }
}
