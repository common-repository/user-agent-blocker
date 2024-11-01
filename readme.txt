=== User Agent Blocker ===
Contributors: adhitya03
Donate link: https://www.paypal.me/Adhitya
Tags: bad robot, block, user-agent, .htaccess
Requires at least: 4.6
Tested up to: 5.2.1
Stable tag: 4.3
Requires PHP: 5.6
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Block robots using it's User-Agent in .htaccess

== Description ==

Block bad robots, unwanted robots, unwanted users or crawler from your site by it's User-Agent using .htaccess.

== Installation ==

1. In your WordPress admin panel, go to Plugins > New Plugin, search for "User Agent Blocker" and click "Install now". Alternatively, download the plugin and upload the contents of user-agent-blocker.zip to your plugins directory, which may be in /wp-content/plugins/.
2. Activate the plugin Google Trends Widget through the 'Plugins' menu in WordPress.
3. Go to Tools > User Agent Blocker and write the user-agent that you want to block in textarea and save changes

== Frequently Asked Questions ==

= I accidentally entered the wrong user-agent so that I could not access my website, what should I do? =

If you accidentally enter the wrong user-agent so that you cannot access your page, then you must open the .htaccess file that is in your domain folder and delete all code that starts from ` # BEGIN USER AGENT BLOCKER ` until ` # END USER AGENT BLOCKER `, save, and reload your page.

= The User-Agent that I want to block does not exist in the table you provide, what should I do?  =

I provide some popular user-agents that might help you, but, if you can not find the user-agent that you need, you have to do google by yourself.

== Screenshots ==

1. User Agent Blocker dashboard area.

== Changelog ==

= 1.0.2 =
* Released: may 30, 2019

= 1.0.1 =
* Released: may 30, 2019

= 1.0.0 =
* Released: may 29, 2019

== Upgrade Notice ==

= 1.0.2 =
* Security improvement : Using Nonces to verify the request
* Security improvement : Validate and Remove special character that can make an Server error 500 and/or Server error 403!
* Security improvement : Sanitize the input before processing

= 1.0.1 =
* New Generic function (and/or define) names to avoid conflict with other plugins or themes
* Editing .htaccess in an optimal way

= 1.0 =
* First version of this plugin