# Access Control Lists (Firewall)

[See working example](acl.php);


## Acl usage

### Possible selectors

| Selector | Description |
|----------|----------------------|
| `route("/some/route")`          | Match a specific route (Wildcards `*` allowed) |
| `role("@roleName")`             | A user must be authenticated and have minimum role |
| `networks("10.0.0.0/8 127.0.0.1/24")` | Match Requesting IP |
| `methods(["GET", "POST"])`            | Match a request type |



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


## Authentication

### Creating SHA-512 passwords

To create a salted secure hashed password use (inside the container):

```
mkpasswd -m sha-512
```

Copy the output of it into the "hash"-section of your password-file.


### Yaml password file format

Create a file `user-passwd.yml` (see [demo](user-passwd.yml))

```
- user: userId1
  hash: $6$Qk0et0h.LQX/NkU5$bqng9ejHYzpNR9xZ7Gf89R1XvNU1Ekf/qCD6P6cTiPmxGd5GoKGjAdXS3falIslX73svTMcQBu25jk0BhdabP.
  role: @admin
  meta:
    some: metadata
    other: metadata
    
...other users...
```


