# ZfrPrerender

[![Build Status](https://travis-ci.org/zf-fr/zfr-prerender.png?branch=master)](https://travis-ci.org/zf-fr/zfr-prerender)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/zf-fr/zfr-prerender/badges/quality-score.png?s=fd78ed5f6dab14beef3884ef3073fd0ce49e2ac5)](https://scrutinizer-ci.com/g/zf-fr/zfr-prerender/)
[![Coverage Status](https://coveralls.io/repos/zf-fr/zfr-prerender/badge.png)](https://coveralls.io/r/zf-fr/zfr-prerender)
[![Latest Stable Version](https://poser.pugx.org/zfr/zfr-prerender/v/stable.png)](https://packagist.org/packages/zfr/zfr-prerender)

Are you using Backbone, Angular, EmberJS, etc, but you're unsure about the SEO implications?

This Zend Framework 2 module uses [Prerender.io](http://www.prerender.io) to dynamically render your JavaScript
pages in your server using PhantomJS.

## Installation

Install the module by typing (or add it to your `composer.json` file):

```sh
$ php composer.phar require zfr/zfr-prerender:1.*
```

## Documentation

### How it works

1. Check to make sure we should show a prerendered page
	1. Check if the request is from a crawler (either agent string or by detecting _escaped_fragment_ query param)
	2. Check to make sure we aren't requesting a resource (js, css, etc...)
	3. (optional) Check to make sure the url is in the whitelist
	4. (optional) Check to make sure the url isn't in the blacklist
2. Make a `GET` request to the [prerender service](https://github.com/collectiveip/prerender) (PhantomJS server) for
the page's prerendered HTML
3. Return that HTML to the crawler

### Customization

ZfrPrerender comes with sane default, but you can customize the module by copying the
[`config/zfr_prerender.global.php.dist`](config/zfr_prerender.global.php.dist) file to your `autoload` folder
(remove the `.dist` extension), and modify it to suit your needs.

#### Prerender URL

By default, ZfrPrerender uses the Prerender.io service deployed at `http://prerender.herokuapp.com`. However, you
may want to [deploy it on your own server](https://github.com/collectiveip/prerender#deploying-your-own). To that
extent, you can customize ZfrPrerender to use your server using the following configuration:

```php
return array(
    'zfr_prerender' => array(
        'prerender_url' => 'http://myprerenderservice.com'
    )
);
```

With this config, here is how ZfrPrerender will proxy the "https://google.com" request:

`GET` http://myprerenderservice.com/https://google.com

#### Crawler user-agents

ZfrPrerender decides to pre-render based on the User-Agent string to check if a request comes from a bot or not. By
default, those user agents are registered: `googlebot`, `yahoo`, `bingbot`, `baidu` and `twitterbot`.

You can add other User-Agent string to evaluate using this sample configuration:

```php
return array(
    'zfr_prerender' => array(
        'crawler_user_agents' => array('yandex', 'msnbot')
    )
);
```

> Note: ZfrPrerender also supports the detection of a crawler through the user of the `_escaped_fragment_` query
param. You can learn more about this on [Google's website](https://developers.google.com/webmasters/ajax-crawling/docs/getting-started).

#### Ignored extensions

ZfrPrerender is configured by default to ignore all the requests for resources with those extensions: `.css`,
`.gif`, `.jpeg`, `.jpg`, `.js`, `.png`, `.less`, `.pdf`, `.doc`, `.txt`, `.zip`, `.mp3`, `.rar`, `.exe`, `.wmv`,
`.doc`, `.avi`, `.ppt`, `.mpg`, `.mpeg`, `.tif`, `.wav`, `.mov`, `.psd`, `.ai`, `.xls`, `.mp4`, `.m4a`, `.swf`,
`.dat`, `.dmg`, `.iso`, `.flv`, `.m4v`, `.torrent`. Those are never pre-rendered.

You can add your own extensions using this sample configuration:

```php
return array(
    'zfr_prerender' => array(
        'ignored_extensions' => array('.less', '.pdf')
    )
);
```

#### Whitelist

Whitelist a single url path or multiple url paths. Compares using regex, so be specific when possible. If a whitelist
is supplied, only url's containing a whitelist path will be prerendered.

Here is a sample configuration that *only* pre-render URLs that contains "/users/":

```php
return array(
    'zfr_prerender' => array(
        'whitelist_urls' => array('/users/*')
    )
);
```

> Note: remember to specify URL here and not ZF2 route names. This occur because ZfrPrerender registers a listener
that happen very early in the MVC process, before the routing is actually done.

#### Blacklist

Blacklist a single url path or multiple url paths. Compares using regex, so be specific when possible. If a blacklist
is supplied, all url's will be pre-rendered except ones containing a blacklist part. Please note that if the referer
is part of the blacklist, it won't be pre-rendered too.

Here is a sample configuration that prerender all URLs *excepting* the ones that contains "/users/":

```php
return array(
    'zfr_prerender' => array(
        'blacklist_urls' => array('/users/*')
    )
);
```

> Note: remember to specify URL here and not ZF2 route names. This occur because ZfrPrerender registers a listener
that happen very early in the MVC process, before the routing is actually done.

### Testing

If you want to make sure your pages are rendering correctly:

1. Open the Developer Tools in Chrome (Cmd + Atl + J)
2. Click the Settings gear in the bottom right corner.
3. Click "Overrides" on the left side of the settings panel.
4. Check the "User Agent" checkbox.
5. Choose "Other..." from the User Agent dropdown.
6. Type googlebot into the input box.
7. Refresh the page (make sure to keep the developer tools open).
