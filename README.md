# Siteimprove eZ Platform Bundle

Bridges the gap between eZ Platform and the Siteimprove Intelligence Platform

## Installation instructions

### Requirements

* eZ Publish 5.4+ / eZ Publish Community Project 2014.07+
* PHP 5.6+

### Use Composer

Using composer you can run this command line to install the bundle:

```bash
$ composer require siteimprove/cms-plugin-ezplatform
```

### Register the bundle

Activate the bundle in `(ezpublish|app)\(EzPublish|app)Kernel.php` file.

```php
// (ezpublish|app)\(EzPublish|app)Kernel.php

public function registerBundles()
{
   ...
   $bundles = array(
       new FrameworkBundle(),
       ...
       new Siteimprove\Bundle\SiteimproveBundle\SiteimproveBundle(),
   );
   ...
}
```

### Install the Legacy extension (if you are using Legacy)

```bash
php (ezpublish|app)/console ezpublish:legacybundles:install_extensions
cd ezpublish_legacy
php bin/php/ezpgenerateautoloads.php -e
```


### Add the routes

```yml
# (ezpublish|app)/config/routing.yml

_siteimprove_routes:
    resource: "@SiteimproveBundle/Controller"
    type:     annotation
    prefix:   /_siteimprove
    
```



### OPTIONNAL

This bundle does server-to-server call to the Siteimprove Intelligence Platform, if for an reason your server is behind 
a HTTP Proxy you can set up the credentials in your config.yml

```yml
# (ezpublish|app)/config/config.yml

siteimprove:
    proxy_settings:
        host: proxy.net
        port: 8080
        user: user
        pass: password
```

## License

[License](LICENSE)
