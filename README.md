# WP chatbot

A simple Wordpress plugin to connect to your favourite chatbot platform.

Works with
 * [api.ai](http://api.ai)
 * [motion.ai](https://motion.ai)
 * [program-o](http://www.program-o.com/)

_...and any other platform that has a REST-based JSON API_

## Roadmap

The current roadmap is simple:

1. Get the plugin ready to go on the .org repo (MVP) - [Current GitHub Project](https://github.com/krilor/wp-chatbot/projects/1)
2. Get it on the repo
3. Double down on new features and extensibility

## How to use

1. Download zip and upload to your Wordpress instance.
2. Activate the plugin and head on over to Settings -> WP chatbot
3. Include `[wp-chatbot]` shortcode wherever you want (currently only supported once per page)

### Settings

There are some base settings that need to be set. In basic terms, the purpose of these settings is to _build the API request that is being used to connect with you chatbot API of choice_. It maps the request and pulls data from the response.

#### The request

* API url
  * The endpoint of the chatbot platform
* Request method
  * GET (most well tested) and POST
* Number of request parameters
 * How many params need to be part of the request
* The request params and their value
 * Each request parameter will require a param-value pair (two input boxes).
 * E.g. when doing a GET call, then the param and value will be appended to the API url like `APIURL?param=value`. Use this to build a proper request to the chatbot platform. Special param values can be used. Se below.
* Number of request headers
  * The number of specific headers that need to be part of the request.
  * These are HTTP Headers
* The request headers and their values
  * Works in a similar way as the request params, but will be included in the HTTP request header.

#### The response

The returned data from the external API must be JSON formatted data (as of now). There is only one option for the response, which is a [JSONpath](http://goessner.net/articles/JsonPath/) used to extract the data.

* Response JSONpath
  * WP Chatbot will extract messages from the returned JSON structure using [JSONpath](http://goessner.net/articles/JsonPath/).
  E.g. for api.ai, such a path can be `$.result.fulfillment.messages[*].speech`

#### Special param values

* WP_CHATBOT_INPUT_MSG
  * Will pass the input message into the parameter
* WP_CHATBOT_CONV
  * Will pass a uniquely created string for the current user session. String is 40 chars and stored in `$_SESSION`
* WP_CHATBOT_USER
  * Will pass a uniquely created string for the current user. String is 40 chars and stored in `$_COOKIE`

## Warning

This plugin is still in development, and are still missing important error handling, validation of input and sanetizing of options. This will be fixed, just keep an eye on the Github Issues.

## Contribute

Fork, branch, pull request

## Wordpress Coding Standards

* `phpcs -p -s -v -n . --standard=codesniffer.ruleset.xml --extensions=php --ignore=./assets/,index.php > sniffer.log`
* `phpcbf -p -s -v -n . --no-patch --standard=codesniffer.ruleset.xml --extensions=php --ignore=./assets/,index.php`
