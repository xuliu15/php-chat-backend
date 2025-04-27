<?php
declare(strict_types=1);
namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Repositories\GroupRepository;

class GroupIndex {
    public function __construct(private GroupRepository $groupRepository) {

    }
    public function __invoke(Request $request, Response $response): Response {
        $data = $this->groupRepository->getGroups();
        $body=json_encode(['message' => 'Groups fetched successfully', 'data' => $data]);
        $response->getBody()->write($body);
        return $response;
    }
}
