Poll
====

Poll webhook for Mattermost.

Test
----

1. Edit `.env` file
1. Initialize database

       ./bin/console doctrine:database:create
       ./bin/console doctrine:schema:update --force

1. Run `php bin/console server:start 127.0.0.1:8000`
1. Open http://localhost:8000 in a browser
