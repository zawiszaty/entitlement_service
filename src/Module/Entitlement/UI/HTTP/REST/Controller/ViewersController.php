<?php

declare(strict_types=1);

namespace App\Module\Entitlement\UI\HTTP\REST\Controller;

use App\Module\Entitlement\Application\UseCase\ViewerAccessToResourceChecker;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ViewersController extends AbstractController
{
    private ViewerAccessToResourceChecker $accessToResourceChecker;

    public function __construct(ViewerAccessToResourceChecker $accessToResourceChecker)
    {
        $this->accessToResourceChecker = $accessToResourceChecker;
    }

    #[Route('/viewers/{viewersId}/available-resource/{resourceId}', name: 'check_viewer_access_to_resource', methods: ['GET'])]
    public function checkViewerAccessToResource(string $viewersId, string $resourceId): Response
    {
        return $this->accessToResourceChecker->canAccess(Uuid::fromString($viewersId), Uuid::fromString($resourceId))
            ? new JsonResponse()
            : throw new AccessDeniedHttpException('Viewer dont have access to resource');
    }
}
