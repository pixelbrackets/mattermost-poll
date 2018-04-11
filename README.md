Poll
====

Poll webhook for Mattermost.

Requirements
------------

  * PHP
  * MySQL

The Mattermost instance calling this service has to be version 4.2 or greater
(provide »Interactive Message Buttons« feature).

Test
----

1. Edit `.env` file
1. Initialize database

       ./bin/console doctrine:database:create
       ./bin/console doctrine:schema:update --force

1. Run `php bin/console server:start 127.0.0.1:8000`
1. Open http://localhost:8000 in a browser
1. Send curl request to create a survey

       curl -H "Content-Type: application/json" -X POST -d '{"channel_id":"niah6qa","user_id":"c3a4cqe3","command":"/poll supper soup salad taco burger"}' http://localhost:8000/new
