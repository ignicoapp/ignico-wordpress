=== Ignico ===
Contributors: ignico, kierzniak
Tags: referrals, affiliate, loyalty, gamification, social selling, compensation, mlm, network marketing, growth hacking
Requires at least: 3.8
Requires PHP: 5.6
Tested up to: 4.9.8
Stable tag: trunk
License: GPL-2.0-or-later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Ignico is **rewards & commission automation engine** that helps businesses create their referral, loyalty, MLM, gamification or social selling program on the top of existing e-commerce platforms or CRM's [(read more about Ignico)](http://igni.co/).

Ignico is a plugin that is built to seamlessly integrate Ignico with [WooCommerce](https://woocommerce.com/) or [Easy Digital Downloads](https://easydigitaldownloads.com/) (WordPress extensions).

= How it works? =

Ignico automatically:

* Loads cookie to the user upon entrance and saves there Referral Code from the URL (comes from affiliate link sent before by one of the brand ambassadors)
* Sends API call with the transaction to Ignico once order in e-commerce platform (based on WooCommerce or Easy Digital Download) is paid. The transaction value equals the amount of the order
* Calculates rewards for brand ambassadors based on e-commerce transactions from the plugin and rewarding rules configured within motivation plans in Ignico admin panel

= Use cases =

With Ignico you can build:

* Referral Program
* Affiliate Program
* Loyalty Program
* Gamification Program
* Social Selling Tool
* Compensation Calculator (for Sales Teams)
* MLM

Powerful as it may seem, Ignico is a truly all-in-one Growth Hacking Platform.

== Installation ==

1. Log in to Wordpress admin panel
2. Visit Plugins > Add New
3. Search for "Ignico"
4. Install and activate "Ignico"

or

1. Download plugin from wordpres.org repository or [release section](https://github.com/ignicoapp/ignico/releases/latest)
2. Upload the ignico directory to your /wp-content/plugins/ directory
3. Activate the plugin through the"‘Plugins" menu in WordPress


= Configiration =

In plugin configuration, you are asked to set:

* Workspace name in Ignico that is set during signing up for [Ignico trial](http://igni.co)
* Client ID and Client secret -> you generate them in Ignico admin panel -> Integrations -> OAuth

Additionally you can also configure cookie settings of the plugin in „Settings” section.

= How to test integration? =

* Go to Ignico admin panel -> Referrals -> Referral links and add a link to your e-commerce store
* Create test user account, go to Ignico admin panel -> Users -> New user
* Log in on user account, go to Referrals -> Referral links and go to the referral link to your store, you should be redirected to URL: http://yourstorename.com?__igrc=REFCODE
* Make a purchase and mare it as paid
* Go to Ignico admin panel -> Actions -> Actions list - you should see your order on the list

== Frequently Asked Questions ==

== Changelog ==

= 0.3.1 =

* Update readme

= 0.3.0 =

= Features =

* Add referral cookie settings

= 0.2.0 =

= Bug Fixes =

* Add success notification when authorization is successful
* Make plugin compatible with PHP 5.6

= Features =

* Add igni.co suffix after workspace field for better ux
* Add validation to admin authorization form

= 0.1.0 =
* Ignico
