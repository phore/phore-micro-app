# Minimalistic Microservice Framework

This documentation is written along the guidelines of educational grade documentation discussed in the 
[infracamp](https://infracamp.com) project.

## Goals

- Minimal Footprint
- No external dependencies
- Clear and small stack trace
- Fluent Api
- Role-Based Authentication

## Quickstart

```index.php:```
```php
$app = new App();
$app->acl->addRule(aclRule()->ALLOW());                     // Allow all requests

$app->router
    ->get("/", function() use ($app) {                      // Define a Action for HTTP-GET-Requests to /
        $app->out("Hello World");
    })
    ->post("/", function(PostData $post) use ($app) {       // Define a Action for HTTP-POST-Requests to /
        $app->out("Your post-data: ". print_r($post));
    });
    
$app->serve();                                              // Run the App
```

## Installation

We suggest using [composer](http://getcomposer.com):

```
composer require phore/micro-app
``` 

## [ACL (Access Control Lists / Firewall)](doc/acl/acl.md) *([Example](doc/acl/acl.php), [FAQ](doc/acl/acl-faq.md))*


***Access Control Lists*** define which User/IP may access which route in
your application. It will initiate the Authentication Process (see Authentication)
or reject the request.

ACLs will be processed from top to bottom. The first rule that matches the request
will win.

### Examples

- ***ALLOW*** if route matches `/api/admin/*` ***AND*** role matches `@admin` ***AND*** network matches `10.0.0.0/8`:
    ```php
    $app->acl->addRule(aclRule()->route("/api/admin/*")->role("@admin")->network("10.0.0.0/8")->ALLOW());
    ```

- ***ALLOW*** access to routes `/admin/*` if authenticated user is in `@admin`-group:
    ```php
    $app->acl->addRule(aclRule()->route("/admin/*")->role("@admin")->ALLOW());
    ```


***By default phore-micro-app will deny all requests. So you have to specify explicitly
which requests to allow***

## Routing

## Assets

## Dependency Injection

## Error Handling

## Authentication & Authorization
