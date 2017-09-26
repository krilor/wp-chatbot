# WP chatbot

Updated version of [wp-chatbot](https://github.com/krilor/wp-chatbot/), simple Wordpress plugin to connect to your favourite chatbot platform.
## V2 notes - update by fr1sk
 * Editable bot image and default one
 * Editable intro header message on chatbot
 * Bot can send youtube videos
 * Bot can send jpg, gif, png images

Works with
 * [api.ai](http://api.ai)
 * [motion.ai](https://motion.ai)
 * [program-o](http://www.program-o.com/)

_...and any other platform that has a REST-based JSON API_

## Roadmap

This plugin is under active development. Current plan is to keep it in GitHub for the foreseeable future.

There is a [GitHub project](https://github.com/krilor/wp-chatbot/projects/2) to track high level features.

## How to use

1. Download a [release](https://github.com/krilor/wp-chatbot/releases) as zip
2. Open up the zip and rename the included folder to `wp-chatbot` (remove the -#.#.# version number)
3. Upload to your Wordpress instance.
4. Activate the plugin and head on over to Settings -> WP chatbot
5. Include `[wp-chatbot]` shortcode wherever you want (currently only supported once per page)

## Getting automatic updates

This plugin can be updated directly from GitHub, in the Wordpress UI, using the Wordpress plugin [GitHub Updater](https://github.com/afragen/github-updater).

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

## Contributors

* [tom](https://github.com/Maniae) - has implemented awesome richtext support for api.ai in [#42](https://github.com/krilor/wp-chatbot/pull/42)!

## Release Notes

* **0.1.1** - bug fixes
  * api.ai encoding
  * allow unsafe urls

## Wordpress Coding Standards

* `phpcs -p -s -v -n . --standard=codesniffer.ruleset.xml --extensions=php --ignore=./assets/,index.php > sniffer.log`
* `phpcbf -p -s -v -n . --no-patch --standard=codesniffer.ruleset.xml --extensions=php --ignore=./assets/,index.php`
