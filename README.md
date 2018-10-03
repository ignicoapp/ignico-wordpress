# Ignico

[![plugin](https://img.shields.io/wordpress/plugin/v/ignico.svg)](https://wordpress.org/plugins/ignico/) [![downloads](https://img.shields.io/wordpress/plugin/dt/ignico.svg)](https://wordpress.org/plugins/ignico/advanced/) [![rating](https://img.shields.io/wordpress/plugin/r/ignico.svg)](https://wordpress.org/support/plugin/ignico/reviews/) ![php support](https://img.shields.io/badge/php%20support-5.6%2C%207.0%2C%207.1%2C%207.2-8892BF.svg)

[![travis](https://img.shields.io/travis/ignicoapp/ignico-wordpress.svg)](https://travis-ci.org/ignicoapp/ignico-wordpress/)

Ignico is **rewards & commission automation engine** that helps businesses create their referral, loyalty, MLM, gamification or social selling program on the top of existing e-commerce platforms or CRM's [(read more about Ignico)](http://igni.co/).

Ignico is a plugin that is built to seamlessly integrate Ignico with [WooCommerce](https://woocommerce.com/) or [Easy Digital Downloads](https://easydigitaldownloads.com/) (WordPress extensions).

## How it works?

Ignico automatically:

* Loads cookie to the user upon entrance and saves there Referral Code from the URL (comes from affiliate link sent before by one of the brand ambassadors),
* Sends API call with the transaction to Ignico once order in e-commerce platform (based on WooCommerce or Easy Digital Download) is paid. The transaction value equals the amount of the order.
* Calculates rewards for brand ambassadors based on e-commerce transactions from the plugin and rewarding rules configured within motivation plans in Ignico admin panel

## Use cases

With Ignico you can build:

* Referral Program
* Affiliate Program
* Loyalty Program
* Gamification Program
* Social Selling Tool
* Compensation Calculator (for Sales Teams)
* MLM

Powerful as it may seem, Ignico is a truly all-in-one **Growth Hacking Platform**.

## Installation :package:
1. Log in to Wordpress admin panel
2. Visit Plugins > Add New
3. Search for "Ignico"
4. Install and activate "Ignico"

or

1. Download plugin from wordpres.org repository or [release section](https://github.com/ignicoapp/ignico-wordpress/releases/latest).
2. Upload the ignico directory to your /wp-content/plugins/ directory
3. Activate the plugin through the "Plugins" menu in WordPress

### Configiration ###

In plugin configuration, you are asked to set:

* Workspace name in Ignico that is set during signing up for [Ignico trial](http://igni.co)
* Client ID and Client secret -> you generate them in Ignico admin panel -> Integrations -> OAuth

Additionally you can also configure cookie settings of the plugin in „Settings” section.

### How to test integration? ###

* Go to Ignico admin panel -> Referrals -> Referral links and add a link to your e-commerce store
* Create test user account, go to Ignico admin panel -> Users -> New user
* Log in on user account, go to Referrals -> Referral links and go to the referral link to your store, you should be redirected to URL: http://yourstorename.com?__igrc=REFCODE
* Make a purchase and mare it as paid
* Go to Ignico admin panel -> Actions -> Actions list - you should see your order on the list

## Contribute :hand:
Please make sure to read the [Contribution guide](https://github.com/ignicoapp/ignico-wordpress/blob/master/CONTRIBUTING.md) before making a pull request.

Thank you to all the people who already contributed to Ignico!

## License :book:
The project is licensed under the [GNU GPLv2 (or later)](https://github.com/ignicoapp/ignico-wordpress/blob/master/LICENSE).

Copyright (c) 2018-present, Ignico Sp. z o.o.
