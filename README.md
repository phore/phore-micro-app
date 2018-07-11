# Minimalistic Microservice Framework

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

## ACL (Access Control Lists / Firewall)


***Access Control Lists*** define which User/IP may access which route in
your application. It will initiate the Authentication Process (see Authentication)
or reject the request.

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

ACLs will be processed from top to bottom. The first rule that matches the request
will win.

The easiest ACL is to ***allow*** Access to all routes:

```php
$app->acl->addRule(aclRule()->route("/*")->ALLOW());
```

You can allow Access to (e.g. api routes) only for specific User-Roles or subnets:

```php
$app->acl->addRule(aclRule()->network("10.0.0.0/8")->ALLOW());
$app->acl->addRule(aclRule()->role("@admin")->ALLOW());
```

To allow only authenticated @admin Group accessing from a private subnet to access 
routes `/api/admin`:

```php
$app->acl->addRule(aclRule()->route("/api/admin/*")->role("@admin")->network("10.0.0.0/8")->ALLOW());
```

***ALLOW*** if route matches `/api/admin/*` ***AND*** role matches `@admin` ***AND** network matches `10.0.0.0/8`

## Routing

## Assets

## Dependency Injection

## Error Handling

## Authentication & Authorization
