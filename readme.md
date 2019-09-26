# laravel-serverless-knative

Package's aim is to provide a possibility to deploy Laravel application as an [Knative](https://knative.dev/) service.

## Installation

Package can be installed into existing project using composer:

`composer require vognev/laravel-serverless-knative`

Also, you need `docker` locally to be able to build runtume image and access to Kubernetes with Knative deployed.

It seems that most easier is to deploy Knative using [Gloo](https://gloo.solo.io/).

See `/manifests` for reference.

For SQS queue you have to install corresponding composer package and deploy [Knative SQS EventSource](https://github.com/knative/eventing-contrib/tree/master/awssqs/samples).

## Initialization
Run `php artisan serverless:install` command.

It will publish:
#### config/serverless.php
This is a configuration file, where you can tweak package's behaviour. Apart from being able to define where to store package's data, it's aim is to configure which php modules should be included in runtime.
#### storage/serverless
On default all runtime-related assets will be published here. Inside of this location you can find `context` folder, holding docker's context to build a runtime.
#### .php.conf.d
In this folder you can tweak php modules options, or add your own `.ini` configs. 

## Building Runtime
`./artisan serverless:runtime` will build and push image containing php and your code.

## Deployment
After steps above are done, you can deploy your project into Knative using pipe:

`./artisan serverless:manifest | kubectl apply -f-`

## Local Development
Anything you can get running: minikube, k3s etc. 
Or use generated runtime image with docker-compose.
