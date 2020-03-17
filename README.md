![Servebolt M2Cache banner with sprinting Cheetah](https://static.servebolt.com/m2cache/servebolt-m2cache.png)

# Servebolt/M2Cache

The Servebolt Magento 2 cache plugin makes sites on [the fastest Magento 2 hosting](https://servebolt.com/platforms/magento-2-hosting/), even faster. It does this by making cache headers for the HTML that allows HTML caching on the web server and in the browser. 

The frontend caches layer on top of the built-in caching in Magento, and eliminates the need for Varnish. It seamlessly integrates with the Servebolt hosting stack that uses nginx as the internet facing web server.

For the admin section and dynamic parts of the store, the frontend cache bypasses requests with the no_cache cookies the cache. 


## Installation

### 1. Configure composer 

```bash
$ composer config repositories.servebolt-m2cache git https://github.com/Servebolt/m2cache.git

$ composer require servebolt/module-m2cache dev-master
```

If you have not already configured composer, you will be prompted for username and password for repo.magento.com. You get these by signing up on [Magento marketplace](https://marketplace.magento.com/).

### 2.1 Activate the plugin using bin/magento

Enable the module and activate it in Magento using the **magento** command. 

```bash
$ magento module:enable Servebolt_M2Cache

$ magento setup:upgrade

$ magento config:set --scope=default --scope-code=0 system/full_page_cache/servebolt_m2c/enabled 1

$ magento cache:flush
```

### 2.2 Activate the plugin using n98-magerun2

Enable the module and activate it in Magento using [n98-magerun2](https://github.com/netz98/n98-magerun2). 

```bash
$ n98 module:enable Servebolt_M2Cache

$ n98 setup:upgrade

$ n98 config:store:set system/full_page_cache/servebolt_m2c/enabled 1

$ n98 cache:flush
```

### 2.3 Enable HTML caching in the Servebolt admin panel

In the [Servebolt Control Panel](https://admin.servebolt.com) the **Caching** setting for your site has to be set to "Static files + Full-Page Cache".

![Image of cache setting in Admin Panel](https://static.servebolt.com/m2cache/magento2-servebolt-admin-panel-cache.png)

In the Magento Admin the cache setting can be set/checked here:
![Image of Magento Admin system configuration page with Full Page Cache settings](https://static.servebolt.com/m2cache/magento2-admin-panel.png)

Use default settings for Caching Application (Built-in cache) and TTL (86400) for public content.

### 2.4 Verify that it works

When the plugin is working correctly, the headers of a cached HTML document will look something like this:

![Image of cache headers as seen in Chrome](https://static.servebolt.com/m2cache/cache-hit.png)

**x-frontend-cache** can have the values HIT, MISS, BYPASS or STALE. If the header is not present at all, the cache setting for the site is not enabled in the Servebolt control panel.  

**expires** is set to a future date, in this example approximately 10 minutes from the current time, which is the 600 second default setting. The cache expiry time is configurable.

## Advanced settings and configuration

The default expires 600, up to 3600 to set to 1 hour.

```bash
$ n98 config:store:set system/full_page_cache/servebolt_m2c/headers/expires/lifetime 3600
```

### Uninstallation

```bash
composer remove servebolt/module-m2cache
```

## Usage 

### Cache Management

The Servebolt Page Cache is made to be unmanaged. This means that the cache will manage itself, and expire pages from the cache within reasonable time. This also means that you do not need to worry about flushing or managing this cache.

The built-in Magento caches on the other hand, can still be managed from the System > Stores > Cache Management page. Emptying this page cache will clear the Magento-internal page cache, but not necessarily expire the Servebolt cache right away. If installed with the default settings, expect the HTML to be refreshed within 10 minutes.

![Image of Cache Management page in Magento admin](https://static.servebolt.com/m2cache/magento2-cache-management.png)


