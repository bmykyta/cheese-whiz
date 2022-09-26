<?php

namespace App\Serializer;

use ApiPlatform\Core\Serializer\SerializerContextBuilderInterface;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class AdminGroupsContextBuilder implements SerializerContextBuilderInterface
{
    public function __construct(
        private readonly SerializerContextBuilderInterface $decorated,
        private readonly AuthorizationCheckerInterface $authorizationChecker
    ) {
    }

    public function createFromRequest(Request $request, bool $normalization, ?array $extractedAttributes = null): array
    {
        $context       = $this->decorated->createFromRequest($request, $normalization, $extractedAttributes);
        $context['groups'] = $context['groups'] ?? [];
        $isAdmin       = $this->authorizationChecker->isGranted('ROLE_ADMIN');

        if ($isAdmin) {
            $context['groups'][] = $normalization ? 'admin:read' : 'admin:write';
        }

        $context['groups'] = array_unique($context['groups']);

        return $context;
    }
}