# Minimalistic Microservice Framework

## Goals

- Minimal Footprint
- No external dependencies
- Clear and small stack trace
- Fluent Api
- Role-Based Authentication


## Quickstart

```index.php:```
```
$app = new App();
$app->acl->addRule(aclRule()->ALLOW()); // Allow all requests

$app->router->get("/", function() {
        app()->out("Hello World");
    })
    ->post("/", function() {
        app()->out("Your post-data: ". print_r($_POST));
    });
$app->serve();
```

## ACL


***Access Control Lists*** define which User/IP may access which route in
your application. It will initiate the Authentication Process (see Authentication)
or reject the request.

***By default phore-micro-app will deny all requests. So you have to specify explicitly
which requests to allow***

ACLs will be processed from top to bottom. The first rule that matches the request
will win.

The easiest ACL is to ***allow*** Access to all routes:

```
$app->acl->addRule(aclRule()->route("/*")->allow());
```

You can allow Access to (e.g. api routes) only for specific User-Roles or subnets:

```
$app->acl->addRule(aclRule()->network("10.0.0.0/8")->allow());
$app->acl->addRule(aclRule()->role("@admin")->allow());
```

To allow only authenticated @admin Group accessing from a private subnet to access 
routes `/api/admin`:

```
$app->acl->addRule(aclRule()->route("/api/admin/*")->role("@admin")->network("10.0.0.0/8")->allow());
```


## Routing

## Assets

## Dependency Injection

## Error Handling

## Authentication & Authorization
