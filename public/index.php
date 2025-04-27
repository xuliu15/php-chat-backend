<?php

use App\Middleware\AddJsonResponseHeader;
use Slim\Factory\AppFactory;
use DI\Container;
use Slim\Handlers\Strategies\RequestResponseArgs;
use App\Controllers\GroupIndex;
use App\Controllers\Groups;
use App\Controllers\Users;
use App\Controllers\UserIndex;
use App\Controllers\Messages;
use App\Controllers\MessageIndex;
use App\Middleware\GetGroups;
use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

require __DIR__ . '/../vendor/autoload.php';

$container = new Container();

AppFactory::setContainer($container);

// Create app
$app = AppFactory::create();

$collector=$app->getRouteCollector();
$collector->setDefaultInvocationStrategy(new RequestResponseArgs);

$app->addBodyParsingMiddleware();

$error_middleware = $app->addErrorMiddleware(true, true, true);
//
$error_handler = $error_middleware->getDefaultErrorHandler();
//
$error_handler->forceContentType('application/json');
$app->add(new AddJsonResponseHeader);

// Add middleware to parse JSON body
// $app->addBodyParsingMiddleware();

// Add error middleware
// $app->addErrorMiddleware(true, true, true);

// $app->get('/', function (Request $request, Response $response) {
//     $response->getBody()->write(json_encode([
//         'status' => 'success',
//         'message' => 'Chat API is running',
//         'endpoints' => [
//             // 'GET /users' => 'Get all users',
//             // 'POST /users' => 'Create a new user',
//             // 'GET /groups' => 'Get all groups',
//             // 'POST /groups' => 'Create a new group',
//             // 'POST /messages' => 'Send a message',
//             'POST /groups/{id}/join' => 'Join a group',
//             'GET /groups/{id}/messages' => 'Get messages from a group',
//             // 'POST /groups/{id}/members' => 'Get group members'
//         ]
//     ]));
//     return $response->withHeader('Content-Type', 'application/json');
// });

// Add routes
// $app->get('/users', function (Request $request, Response $response) {
//     $db = new Database('chat.db', false);
//     $users = $db->query("SELECT * FROM users")->fetchAll(PDO::FETCH_ASSOC);
    
//     $response->getBody()->write(json_encode([
//         'status' => 'success',
//         'data' => $users
//     ]));
//     return $response->withHeader('Content-Type', 'application/json');
// });

// $app->post('/users', function (Request $request, Response $response) {
//     $data = $request->getParsedBody();
    
//     // Validate input
//     if (!isset($data['username']) || empty($data['username'])) {
//         $response->getBody()->write(json_encode([
//             'status' => 'error',
//             'message' => 'Username is required'
//         ]));
//         return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
//     }

//     $db = new Database('chat.db', false);
    
//     try {
//         // Check if username already exists
//         $existingUser = $db->query("SELECT id FROM users WHERE username = ?", [$data['username']])->fetch();
//         if ($existingUser) {
//             $response->getBody()->write(json_encode([
//                 'status' => 'error',
//                 'message' => 'Username already exists'
//             ]));
//             return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
//         }

//         // Create new user
//         $db->query("INSERT INTO users (username) VALUES (?)", [$data['username']]);
//         $userId = $db->getConnection()->lastInsertId();
        
//         $response->getBody()->write(json_encode([
//             'status' => 'success',
//             'data' => ['id' => $userId, 'username' => $data['username']]
//         ]));
//         return $response->withHeader('Content-Type', 'application/json');
//     } catch (PDOException $e) {
//         $response->getBody()->write(json_encode([
//             'status' => 'error',
//             'message' => 'Database error: ' . $e->getMessage()
//         ]));
//         return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
//     }
// });

// Add more routes
// $app->get('/groups', function (Request $request, Response $response) {
//     $db = new Database('chat.db', false);
//     $groups = $db->query("SELECT * FROM groups")->fetchAll(PDO::FETCH_ASSOC);
    
//     $response->getBody()->write(json_encode([
//         'status' => 'success',
//         'data' => $groups
//     ]));
//     return $response->withHeader('Content-Type', 'application/json');
// });

// $app->post('/groups', function (Request $request, Response $response) {
//     $data = $request->getParsedBody();
    
//     // Validate input
//     if (!isset($data['name']) || empty($data['name'])) {
//         $response->getBody()->write(json_encode([
//             'status' => 'error',
//             'message' => 'Group name is required'
//         ]));
//         return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
//     }

//     $db = new Database('chat.db', false);
    
//     try {
//         // Check if group name already exists
//         $existingGroup = $db->query("SELECT id FROM groups WHERE name = ?", [$data['name']])->fetch();
//         if ($existingGroup) {
//             $response->getBody()->write(json_encode([
//                 'status' => 'error',
//                 'message' => 'Group name already exists'
//             ]));
//             return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
//         }

//         // Create new group
//         $db->query("INSERT INTO groups (name) VALUES (?)", [$data['name']]);
//         $groupId = $db->getConnection()->lastInsertId();
        
//         $response->getBody()->write(json_encode([
//             'status' => 'success',
//             'data' => ['id' => $groupId, 'name' => $data['name']]
//         ]));
//         return $response->withHeader('Content-Type', 'application/json');
//     } catch (PDOException $e) {
//         $response->getBody()->write(json_encode([
//             'status' => 'error',
//             'message' => 'Database error: ' . $e->getMessage()
//         ]));
//         return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
//     } 
// });
$app->group('/api', function (RouteCollectorProxy $group) {
        $group->get('/groups', GroupIndex::class);
        $group->post('/groups', [Groups::class, 'createGroup']);
        $group->post('/groups/join', [Groups::class, 'joinGroup']);
        $group->post('/users', [Users::class, 'createUser']);
        $group->get('/users', UserIndex::class);
        $group->post('/groups/messages', [Messages::class, 'createMessage']);
        // $group->get('/groups/{groupId:\d+}',[Groups::class, 'showGroup'])->add(GetGroups::class);
        $group->get('/groups/{groupId:\d+}/messages',[Groups::class, 'getGroupMessages'])->add(GetGroups::class);
});
// Add message routes
// $app->post('/messages', function (Request $request, Response $response) {
//     $data = $request->getParsedBody();
    
//     // Validate input
//     if (!isset($data['user_id']) || !isset($data['group_id']) || !isset($data['content']) || empty($data['content'])) {
//         $response->getBody()->write(json_encode([
//             'status' => 'error',
//             'message' => 'User ID, Group ID and content are required'
//         ]));
//         return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
//     }

//     $db = new Database('chat.db', false);
    
//     try {
//         // First check if user is a member of the group
//         $isMember = $db->query(
//             "SELECT 1 FROM group_members WHERE user_id = ? AND group_id = ?",
//             [$data['user_id'], $data['group_id']]
//         )->fetch();

//         if (!$isMember) {
//             $response->getBody()->write(json_encode([
//                 'status' => 'error',
//                 'message' => 'User must be a member of the group to send messages'
//             ]));
//             return $response->withStatus(403)->withHeader('Content-Type', 'application/json');
//         }

//         // Send message
//         $db->query(
//             "INSERT INTO messages (user_id, group_id, content) VALUES (?, ?, ?)",
//             [$data['user_id'], $data['group_id'], $data['content']]
//         );
//         $messageId = $db->getConnection()->lastInsertId();
        
//         $response->getBody()->write(json_encode([
//             'status' => 'success',
//             'data' => [
//                 'id' => $messageId,
//                 'user_id' => $data['user_id'],
//                 'group_id' => $data['group_id'],
//                 'content' => $data['content']
//             ]
//         ]));
//         return $response->withHeader('Content-Type', 'application/json');
//     } catch (PDOException $e) {
//         $response->getBody()->write(json_encode([
//             'status' => 'error',
//             'message' => 'Failed to send message'
//         ]));
//         return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
//     }
// });

// Get messages from a group
// $app->get('/groups/{id}/messages', function (Request $request, Response $response, array $args) {
//     $db = new Database('chat.db', false);
    
//     try {
//         // Check if group exists
//         $group = $db->query("SELECT id FROM groups WHERE id = ?", [$args['id']])->fetch();
//         if (!$group) {
//             $response->getBody()->write(json_encode([
//                 'status' => 'error',
//                 'message' => 'Group not found'
//             ]));
//             return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
//         }

//         // Get messages with user information
//         $messages = $db->query(
//             "SELECT m.*, u.username 
//              FROM messages m 
//              JOIN users u ON m.user_id = u.id 
//              WHERE m.group_id = ? 
//              ORDER BY m.created_at DESC",
//             [$args['id']]
//         )->fetchAll(PDO::FETCH_ASSOC);
        
//         $response->getBody()->write(json_encode([
//             'status' => 'success',
//             'data' => $messages
//         ]));
//         return $response->withHeader('Content-Type', 'application/json');
//     } catch (PDOException $e) {
//         $response->getBody()->write(json_encode([
//             'status' => 'error',
//             'message' => 'Database error: ' . $e->getMessage()
//         ]));
//         return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
//     }
// });
// Join a group
// $app->post('/groups/{id}/join', function (Request $request, Response $response, array $args) {
//     $data = $request->getParsedBody();
//     $db = new Database('chat.db', false);
    
//     try {
//         // First check if the user and group exist
//         $user = $db->query("SELECT id FROM users WHERE id = ?", [$data['user_id']])->fetch();
//         $group = $db->query("SELECT id FROM groups WHERE id = ?", [$args['id']])->fetch();
        
//         if (!$user) {
//             $response->getBody()->write(json_encode([
//                 'status' => 'error',
//                 'message' => 'User not found',
//             ]));
//             return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
//         }
        
//         if (!$group) {
//             $response->getBody()->write(json_encode([
//                 'status' => 'error',
//                 'message' => 'Group not found',
//             ]));
//             return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
//         }

//         // Try to join
//         $db->query(
//             "INSERT INTO group_members (user_id, group_id) VALUES (?, ?)",
//             [$data['user_id'], $args['id']]
//         );
        
//         // Verify the join
//         $membership = $db->query(
//             "SELECT * FROM group_members WHERE user_id = ? AND group_id = ?",
//             [$data['user_id'], $args['id']]
//         )->fetch();
        
//         $response->getBody()->write(json_encode([
//             'status' => 'success',
//             'message' => 'Joined group successfully'
//         ]));
//         return $response->withHeader('Content-Type', 'application/json');
//     } catch (PDOException $e) {
//         $response->getBody()->write(json_encode([
//             'status' => 'error',
//             'message' => 'Failed to join group'
//         ]));
//         return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
//     }
// });

// Get group members
// $app->get('/groups/{id}/members', function (Request $request, Response $response, array $args) {
//     $db = new Database('chat.db', false);
    
//     $members = $db->query(
//         "SELECT u.id, u.username 
//          FROM users u 
//          JOIN group_members gm ON u.id = gm.user_id 
//          WHERE gm.group_id = ?",
//         [$args['id']]
//     )->fetchAll(PDO::FETCH_ASSOC);
    
//     $response->getBody()->write(json_encode([
//         'status' => 'success',
//         'data' => $members
//     ]));
//     return $response->withHeader('Content-Type', 'application/json');
// });

// Run app
$app->run();