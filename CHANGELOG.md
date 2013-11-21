# 2.1.0

* Allow to add a Prerender token in module options (for analytics)

# 2.0.0

* [BC] Remove Google Bot, Yahoo and BingBot from the crawler's list, because those search engines
support _escaped_fragment_ and want to ensure people aren't penalized for cloaking
* Prerender listener now triggers two events: "prerender.pre" and "prerender.post". This allows you to
cache the response and return it without hitting the Prerender service
* The priority the listener is registered has been lowered from 10000 to 1000

# 1.1.2

* Add "xml" extension to blacklist to prevent a problem that can occur with sitemap

# 1.1.1

* Add Twitterbot User-Agent

# 1.1.0

* Add support for _escaped_fragment_ query param for detecting a bot
* Add Facebook crawler User-Agent

# 1.0.0

* Initial release
