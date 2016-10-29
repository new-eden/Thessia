# Warning
This is still early beta stuff, so beware!

# Thessia

_[Embrace Eternity...] (https://www.youtube.com/watch?v=vMEWIl_WwVA)_

Thessia is a small framework to build websites.
It uses Slim3 and several Symfony packages

# Requirements
PHP7

Redis

MongoDB

Composer

# Installation
1. Get composer from `https://getcomposer.org/`
2. Install packages with composer: `php7.0 composer.phar install -o`
3. To setup the MongoDB Database and Collections run `php7.0 Thessia setup:site` **(DOES NOT CURRENTLY EXIST OR WORK - Manually create database (thessia) and collections (alliances, characters, corporations, killmails, marketPrices))**
4. Setup nginx as you would any php project, or use the built in webserver (For development, the built in is easier)
5. For Cronjobs/Resque/Websocket look at the # Supervisor section

# Run local server
To run the local server, on the unix shell, make sure that you have php7 cgi installed, and have setup mongodb and redis aswell.
Then simply run: `php7.0 Thessia run:server --host <your ip> --port <a port>`

And that's it, now you're running Thessia on your computer/server, with it's built in webserver.

Default its set to debugging, if you want to use it in production you can use `--app-env=prod --debug=0 --concurrent-requests=1`

# Supervisor
Stuff to come here once everything works.

# License

MIT Licensed
