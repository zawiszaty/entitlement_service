<?php

declare(strict_types=1);

namespace App\Module\Entitlement\UI\HTTP\REST\Controller;

use App\Module\Entitlement\Application\UseCase\AssignResourceToEntitlement;
use App\Module\Entitlement\Application\UseCase\ResourceCreator;
use Munus\Collection\GenericList;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ResourcesController extends AbstractController
{
    private ResourceCreator $creator;
    private AssignResourceToEntitlement $assignResourceToEntitlement;

    public function __construct(ResourceCreator $creator, AssignResourceToEntitlement $assignResourceToEntitlement)
    {
        $this->creator = $creator;
        $this->assignResourceToEntitlement = $assignResourceToEntitlement;
    }

    #[Route('/resources', name: 'create_resource', methods: ['POST'])]
    public function createResourceAction(Request $request): Response
    {
        $ids = GenericList::ofAll($request->request->get('entitlementsIds'));
        $ids = $ids->map(function (string $id) {
            return Uuid::fromString($id);
        });
        $model = $this->creator->create(
            $request->request->get('name'),
            $request->request->get('category'),
            $ids
        );

        return new JsonResponse($model);
    }

    #[Route('/resources/{resourceId}', name: 'assign_entitlement_to_resource', methods: ['PUT'])]
    public function assignEntitlementToResourceAction(Request $request, string $resourceId): Response
    {
        $resourceId = Uuid::fromString($resourceId);
        $ids = GenericList::ofAll($request->request->get('entitlementsIds'));
        $ids->forAll(function (string $id) use ($resourceId) {
            $this->assignResourceToEntitlement->assign(Uuid::fromString($id), $resourceId);
        });

        return new JsonResponse();
    }
}
