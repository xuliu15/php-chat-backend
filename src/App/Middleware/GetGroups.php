<?php
declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Routing\RouteContext;
use App\Repositories\GroupRepository;
use Slim\Exception\HttpNotFoundException;

class GetGroups {
    public function __construct(private GroupRepository $groupRepository) {

    }
    public function __invoke(Request $request, RequestHandler $handler): Response {
        $context = RouteContext::fromRequest($request);
        $route = $context->getRoute();
        $groupId = $route->getArgument('groupId');
        $group = $this->groupRepository->getGroupById((int) $groupId);
        if ($group === false) {
            throw new HttpNotFoundException($request,
            message: "Group not found");
        }
        $request = $request->withAttribute('group', $group);
        return $response = $handler->handle($request);
}
}   