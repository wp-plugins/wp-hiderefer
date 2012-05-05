=== WP-HideRefer ===
Contributors: ulfben
Donate link: http://www.amazon.com/gp/registry/wishlist/2QB6SQ5XX2U0N/105-3209188-5640446?reveal=unpurchased&filter=all&sort=priority&layout=standard&x=11&y=10
Tags: anonymise, anonymize, blank referrer, hide referrer, mask, privacy, referral, hiderefer, referer, private
Requires at least: 3.3.2
Tested up to: 3.3.2
Stable tag: 1.1
License: GPLv2 or later

WP-HideRefer adds proxies to your outgoing links, keeping your site private! 

== Description ==

When your readers follow links from your blog, the linked site can see where they come from. Thus; your blog is known by every site you've ever linked to.

WP-HideRefer adds proxies to your outgoing links, keeping your site private! 

There are many plugins to anonymize links. What makes WP-HideRefer better is:

* it's 100% WordPress API compliant
* it's entirely server-side (= cacheable & no JavaScript!)
* therefore; supports [infinite-scroll](http://wordpress.org/extend/plugins/infinite-scroll/) (AJAX / streaming)
* it correctly filters your feeds and comments
* [it can handle your manually anonymized links](http://wordpress.org/extend/plugins/wp-hiderefer/faq/)!

If you value [my plugins](http://profiles.wordpress.org/users/ulfben/), please help me out by [Flattr-ing them](http://flattr.com/thing/367557/Support-my-WordPress-plugins)! Or perhaps [send me a book](http://www.amazon.com/gp/registry/wishlist/2QB6SQ5XX2U0N/105-3209188-5640446?reveal=unpurchased&filter=all&sort=priority&layout=standard&x=11&y=10)? Used ones are fine! :)

//*[Ulf Benjaminsson](http://www.ulfben.com)*

(Please note that WP-HideRefer requires PHP 5 or newer.)

== Changelog ==

= 1.1 =
... don't proxy relative links, silly. :)

= 1.0 =
Public release.

== Upgrade Notice ==

= 1.2 = 
Update to handle internal (relative) links correctly.

= 1.0 =
First release.

== Frequently Asked Questions ==
= What's the White List for? =
Once the plugin is activated it redirects all links via a proxy, except for the URLs you enter in this list.

If you used to manually add proxies to your links, add those proxies to the White List to avoid double-proxying. You should also white list your own domain.

= What anonymizing services are supported? =
You can use any service which runs of URL parameters. 

== Installation ==

1. Extract the `wp-hiderefer`-folder and transfer it to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Go to Settings->WP-HideRefer to adjust settings.

== Screenshots ==

1. The settings panel

== Other Notes ==
Copyright (C) 2012- Ulf Benjaminsson (my first name at ulfben dot com).

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
