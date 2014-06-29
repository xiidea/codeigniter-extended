Using PHP Built-in server
===========================
CIX Bundled with a router script to use php built-in web server. Just run the following command.

    php bin/cli server:run

If you get the error There are no commands defined in the "server" namespace., then you are probably using PHP 5.3. That's ok! But the built-in web server is only available for PHP 5.4.0 or higher.