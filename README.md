# bunq-chat
 This is a chat application backend in PHP, using Slim framework. 

 ## Features:
 - create chat groups
 - join chat groups
 - send messages in a group
 - list messages within a group
 - list all groups
 - list all users

## How to run:

install dependencies:

`composer install`

run on a local server:

`php -S localhost:8080 -t public`

To create a user:

`curl -X POST "http://localhost:8080/api/users" \
     -H "Content-Type: application/json" \
     -d '{"username": "Joy"}'`

To create a group:

`curl -X POST "http://localhost:8080/api/groups" \
     -H "Content-Type: application/json" \
     -d '{"name": "Joy Chatroom"}'`

To join a group:

`curl -X POST "http://localhost:8080/api/groups/join" \
     -H "Content-Type: application/json" \
     -d '{"userId": 1, "groupId": 1}'`

Note: replace userId and group_Id with the IDs from previous responses

To send a message in a group:

`curl -X POST "http://localhost:8080/api/groups/messages" \
     -H "Content-Type: application/json" \
     -d '{"userId": 1, "groupId": 1, "content": "Hello, this is a test message!"}'`

Note: replace userId and group_Id with the IDs from previous responses

To list all groups:

`curl -X GET "http://localhost:8080/api/groups"`

To list all users:

`curl -X GET "http://localhost:8080/api/users"`

## How to run unittest:
`./vendor/bin/phpunit tests/Repositories`

## External libraries:

vlucas/valitron: a validation library to validate the inputs for sending messages in a group. UserId, groupId and content are required, otherwise it returns an error message. 

php-di/php-di: a dependency injection library to handle creation and injection of dependencies.

## Limitations and improvements:
- limited capbilities to handle edge cases.
- limited error handling.
- limited code formatting. 

## Credits
This project is inspired by https://github.com/daveh/slim-rest-api-example











