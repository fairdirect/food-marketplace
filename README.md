# Epelia

## 1. Introduction

**Overview.** Epelia is an open source food marketplace platform. You can find a detailed description of its functionality (in German) on [fairdirect.org/erzeugermarkt](https://fairdirect.org/erzeugermarkt). We also provide an online demo version of the Epelia software at [demo.regiomal.com](http://demo.regiomal.com). Please [contact us by e-mail](mailto:mail@fairdirect.org) to get access to it.


**Features.**

* **Marketplace.** Users get a single user account and can use it to shop in all of the webshops of the platform. This is similar to eBay, Amazon and other e-commerce platforms.

* **Specialized on food.** Unlike any other open source marketplace software that we know (like [Fairmondo](https://github.com/fairmondo/fairmondo)), Epelia is specialized software for online food sales. As such, it provides features such as searching for allergene-free products (by allergene), designation of products from controlled organic farming etc..

* **Shopping lists.** For customers who want to view sellers' offers online and then make the actual purchases (for example) at the next farmers market where all the sellers are present, there is a feature to put items on a shopping list rather than into a shopping cart.

* **One checkout, one payment.** Unlike other e-commerce platforms, customers only have to go through a single checkout and payment process even though their shopping cart contains items from many different sellers. Payments are forwarded to the shops by the marketplace operator once a month, and the software provides a dedicated feature for that.

* **Online orders with pickup.** A typical use case for farmers markets is that customers want to pre-order products online (such as discounted items with limited availability) and then pick up their purchases in person at the next farmers market. The Epelia software supports that with a dedicated delivery option "pickup".

* **Online orders with combined shipment.** Another typical use case for "online" farmers markets is that the marketplace operator organizes an efficient and low-cost combined shipment of customer orders from the farmers market to those customers who selected shipment rather than pickup as delivery option. In this case, a customer will receive one single package with all the products they ordered, even though these might come from many different sellers of ther farmers market.


**Technology.** Epelia is written in PHP, using the Zend Framework and the Bootstrap CSS framework. It uses PostgreSQL as its database.


**Trademarks.** Epelia is a registered trademark of [Micha Gattinger](mailto:mail@michagattinger.de). You are welcome to create and distribute derivative versions of this software, but you have to give your derivative a different name. This means, replace all occurrences of "Epelia" with a different name, replace all occurrences of the Epelia logo with your own logo, and do not give any impression that your derivative version is the official Epelia software or endorsed by it.


**Copyright.** Copyright 2014-2020 by the Epelia developers. Special thanks go to Sebastian Hösel for [his contributions](https://github.com/Fairdirect/epelia/commits?author=hoesel) :-)

The copyrights for [all commits up to and including 2020-02-12](https://github.com/Fairdirect/epelia/tree/2af9da356b60f90b79e5900dc883c1184ed32b75) are owned by [Micha Gattinger](mailto:mail@michagattinger.de). Parts of that code has been developed by other develoeprs, but all copyrights are owned by Micha Gattinger.


## 2. Installation

There is nothing special about it:

1. Create an empty database initialized with [`epelia.sql`](https://github.com/Fairdirect/epelia/blob/master/docs/epelia.sql).

2. Configure database access etc. in ['application/configs/application.ini'](https://github.com/Fairdirect/epelia/blob/master/application/configs/application.ini).

3. Configure your webserver to serve directory [`public/`](https://github.com/Fairdirect/epelia/tree/master/public) as the document root directory of the software.

4. Access the website.

Before providing a platform based on the Epelia software to the public, please update all dependencies. There might be security vulnerabilities in the current state of the software as the dependencies have not been updated in quite some time.


## 3. Usage

TODO