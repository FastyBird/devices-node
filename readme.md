# FastyBird devices node

[![Build Status](https://img.shields.io/travis/FastyBird/devices-node.svg?style=flat-square)](https://travis-ci.org/FastyBird/devices-node)
[![Code coverage](https://img.shields.io/coveralls/FastyBird/devices-node.svg?style=flat-square)](https://coveralls.io/r/FastyBird/devices-node)
![PHP from Travis config](https://img.shields.io/travis/php-v/fastybird/devices-node?style=flat-square)
[![Licence](https://img.shields.io/packagist/l/FastyBird/devices-node.svg?style=flat-square)](https://packagist.org/packages/FastyBird/devices-node)
[![Downloads total](https://img.shields.io/packagist/dt/FastyBird/devices-node.svg?style=flat-square)](https://packagist.org/packages/FastyBird/devices-node)
[![Latest stable](https://img.shields.io/packagist/v/FastyBird/devices-node.svg?style=flat-square)](https://packagist.org/packages/FastyBird/devices-node)
[![PHPStan](https://img.shields.io/badge/PHPStan-enabled-brightgreen.svg?style=flat-square)](https://github.com/phpstan/phpstan)

## What is FastyBird devices node?

Devices node is a core microservice for devices which is managing all connected devices, managing basic devices logic and exposing them for clients.

FastyBird devices node is an Apache2 licensed distributed microservice, developed in PHP with [Nette framework](https://nette.org).

## Requirements

FastyBird devices node is tested against PHP 7.3 and [ReactPHP http](https://github.com/reactphp/http) 0.8 event-driven, streaming plaintext HTTP server and [RabbitMQ](https://www.rabbitmq.com/) 3.7 message broker

## Getting started

> **NOTE:** If you don't want to install it manually, try [docker image](#install-with-docker)

The best way to install **fastybird/devices-node** is using [Composer](http://getcomposer.org/). If you don't have Composer yet, [download it](https://getcomposer.org/download/) following the instructions.
Then use command:

```sh
$ composer create-project --no-dev fastybird/devices-node path/to/install
$ cd path/to/install
```

Everything required will be then installed in the provided folder `path/to/install`

This microservice is composed from one console command.

##### HTTP server

```sh
$ vendor/bin/fb-console fb:node:server:start
```

This server is listening for incoming http api request messages from clients.
And also is listening for new data from exchange bus from other microservices.

## Install with docker

![Docker Image Version (latest by date)](https://img.shields.io/docker/v/fastybird/devices-node?style=flat-square)
![Docker Image Size (latest by date)](https://img.shields.io/docker/image-size/fastybird/devices-node?style=flat-square)
![Docker Cloud Build Status](https://img.shields.io/docker/cloud/build/fastybird/devices-node?style=flat-square)

Docker image: [fastybird/devices-node](https://hub.docker.com/r/fastybird/devices-node/)

### Use docker hub image

```bash
$ docker run -d -it --name devices fastybird/devices-node:latest
```

### Generate local image

```bash
$ docker build --tag=devices-node .
$ docker run -d -it --name devices-node devices-node
```

## Configuration

This microservices is preconfigured for default connections, but your infrastructure could be different.

Configuration could be made via environment variables:

| Environment Variable | Description |
| ---------------------- | ---------------------------- |
| `FB_NODE_PARAMETER__RABBITMQ_HOST=127.0.0.1` | RabbitMQ host address |
| `FB_NODE_PARAMETER__RABBITMQ_PORT=5672` | RabbitMQ access port |
| `FB_NODE_PARAMETER__RABBITMQ_VHOST=/` | RabbitMQ vhost |
| `FB_NODE_PARAMETER__RABBITMQ_USERNAME=guest` | Username |
| `FB_NODE_PARAMETER__RABBITMQ_PASSWORD=guest` | Password |
| | |
| `FB_NODE_PARAMETER__DATABASE_VERSION=5.7` | MySQL server version |
| `FB_NODE_PARAMETER__DATABASE_HOST=127.0.0.1` | MySQL host address |
| `FB_NODE_PARAMETER__DATABASE_PORT=3306` | MySQL access port |
| `FB_NODE_PARAMETER__DATABASE_DBNAME=devices_node` | MySQL database name |
| `FB_NODE_PARAMETER__DATABASE_USERNAME=root` | Username |
| `FB_NODE_PARAMETER__DATABASE_PASSWORD=` | Password |
| | |
| `FB_NODE_PARAMETER__SERVER_ADDRESS=0.0.0.0` | HTTP server host address |
| `FB_NODE_PARAMETER__SERVER_PORT=8000` | HTTP server access port |

> **NOTE:** In case you are not using docker image or you are not able to configure environment variables, you could edit configuration file `./config/default.neon`

## Initialization

This microservice is using database, so you have to initialise basic database schema. It could be done via shell command:

```sh
$ php vendor/bin/doctrine orm:schema-tool:create
```

After this steps, microservice could be started with [server command](#http-server)

## Feedback

Use the [issue tracker](https://github.com/FastyBird/devices-node/issues) for bugs or [mail](mailto:info@fastybird.com) or [Tweet](https://twitter.com/fastybird) us for any idea that can improve the project.

Thank you for testing, reporting and contributing.

## Changelog

For release info check [release page](https://github.com/FastyBird/devices-node/releases)

## Maintainers

<table>
	<tbody>
		<tr>
			<td align="center">
				<a href="https://github.com/akadlec">
					<img width="80" height="80" src="https://avatars3.githubusercontent.com/u/1866672?s=460&amp;v=4">
				</a>
				<br>
				<a href="https://github.com/akadlec">Adam Kadlec</a>
			</td>
		</tr>
	</tbody>
</table>

***
Homepage [http://www.fastybird.com](http://www.fastybird.com) and repository [http://github.com/fastybird/devices-node](http://github.com/fastybird/devices-node).
