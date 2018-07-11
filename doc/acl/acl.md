# Access Control Lists (Firewall)

[See working example](acl.php);

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


