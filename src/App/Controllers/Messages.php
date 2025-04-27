<?php

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Repositories\MessageRepository;
use Valitron\Validator;

class Messages {
    public function __construct(private MessageRepository $messageRepository, 
                                private Validator $validator)
     {
        $this->validator->mapFieldsRules([
            'userId' => ['required'],
            'groupId' => ['required'],
            'content' => ['required']
        ]);

    }
    public function createMessage(Request $request, Response $response): Response {
        try {
            $body = $request->getParsedBody();
            
            $this->validator = $this->validator->withData($body);
            if (!$this->validator->validate()) {
                $response->getBody()->write(json_encode([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $this->validator->errors()
                ]));
                return $response->withStatus(422);
            }

            $message = $this->messageRepository->createMessage($body);
            
            $response->getBody()->write(json_encode([
                'status' => 'success',
                'data' => $message
            ]));
            return $response->withStatus(201);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]));
            return $response->withStatus(400);
        }
    }
}