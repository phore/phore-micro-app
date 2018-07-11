# Minimalistic Microservice Framework

## Goals

- Minimal Footprint
- No external dependencies
- Clear and small stack trace
- Fluent Api


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



## Routing

## Assets

## Dependency Injection

## Error Handling

## Authentication & Authorization
