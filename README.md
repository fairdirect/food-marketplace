# Fairdirect Food Marketplace

**[1. Introduction](#1-introduction)**

**[2. Repository Layout](#2-repository-layout)**

**[3. Installation](#3-installation)**

**[4. Usage](#4-usage)**

## 1. Introduction

**Overview.** The Fairdirect Food Marktplace platform is an open source marketplace software specially made for online food sales and food donations. It comes in two variants, contained in branches: one for normal food sales, and one as a platform to offer food as donations to charitable organizations (such as Germany's "Tafeln"). You can find a more detailed description of the functionality (in German) on [fairdirect.org/erzeugermarkt](https://fairdirect.org/erzeugermarkt).

**Online demos.** We operate several online demo and live versions of this software, where you can see it in action:

* [demo.regiomal.com](http://demo.regiomal.com) shows the software as a regional food marketpalce. Please [contact us by e-mail](mailto:mail@fairdirect.org) to get access.

* [lebensmittelspende.org](http://lebensmittelspende.org) uses this software to provide a live platform for facilitating food donations from food shops to charitable organizations


**Features.**

* **Marketplace.** Users get a single user account and can use it to shop in all of the webshops of the platform. This is similar to eBay, Amazon and other e-commerce platforms.

* **Specialized on food.** Unlike any other open source marketplace software that we know (like [Fairmondo](https://github.com/fairmondo/fairmondo)), Epelia is specialized software for online food sales. As such, it provides features such as searching for allergene-free products (by allergene), designation of products from controlled organic farming etc..

* **Shopping lists.** For customers who want to view sellers' offers online and then make the actual purchases (for example) at the next farmers market where all the sellers are present, there is a feature to put items on a shopping list rather than into a shopping cart.

* **One checkout, one payment.** Unlike other e-commerce platforms, customers only have to go through a single checkout and payment process even though their shopping cart contains items from many different sellers. Payments are forwarded to the shops by the marketplace operator once a month, and the software provides a dedicated feature for that.

* **Online orders with pickup.** A typical use case for farmers markets is that customers want to pre-order products online (such as discounted items with limited availability) and then pick up their purchases in person at the next farmers market. The Epelia software supports that with a dedicated delivery option "pickup".

* **Online orders with combined shipment.** Another typical use case for "online" farmers markets is that the marketplace operator organizes an efficient and low-cost combined shipment of customer orders from the farmers market to those customers who selected shipment rather than pickup as delivery option. In this case, a customer will receive one single package with all the products they ordered, even though these might come from many different sellers of ther farmers market.

* **Offering, ordering and delivering donations of surplus food.** In this case, the marketplace is not used for selling but for utlizing surplus food. Like any marketplace, there can be several parties offering and several parties ordering what is on offer. The development of these features was initiated during the [#WeVsVirus Hackathon](https://wirvsvirushackathon.org/) in March 2020 as project [Tafeln Reorganisation Akquise](https://devpost.com/software/online-lebensmittel-aquise-fur-die-tafeln-fairdirect). See there for a detailed description of what this feature will eventually contain.


**Technology.** This platform written in PHP, using the Zend Framework and the Bootstrap CSS framework. It uses PostgreSQL as its database.

**Copyright.** Copyright 2012-2020 by the Food Marketplace developers. Alphabetical list of contributors:

* Christian Beck
* David Goraj
* Matthias Ansorg
* Sebastian Hösel. Wrote the complete initial version, back then for epelia.com.

This software has been derived from the open source marketplace software "Epelia", which has been released as free software on 2020-03-20 under the AGPL licence. Due to this history, the copyrights for all commits up to and including 2020-02-12 are owned by Micha Gattinger. Parts of the code in these commits has been developed by other develoeprs, but all copyrights has been transferred to Micha Gattinger for these contributions.

The copyrights for [all commits up to and including 2020-02-12](https://github.com/Fairdirect/epelia/tree/2af9da356b60f90b79e5900dc883c1184ed32b75) are owned by [Micha Gattinger](mailto:mail@michagattinger.de). Parts of that code has been developed by other develoeprs, but all copyrights are owned by Micha Gattinger.

**Licence.** This platform, as available on Github under [fairdirect/food-marketplace](https://github.com/fairdirect/food-marketplace), is free software under the terms of the GNU Affero General Public License v3.0 license, or at your option any later version. For details please consult the licence terms in file [`LICENSE`](https://github.com/fairdirect/food-marketplace/blob/master/LICENSE).

This licence does not cover about 10% additional code that has not yet been published because it contains some third-party non-free code. Before inclusion, it has to be replaced, or thorougly reviewed and partially rewritten. So far, it is contained in the non-public branches with the `+nonfree` suffix. The affected code is some view layer code and the CSS stylesheets.

The unreleased code is necessary for a functional installation. However, our current demo sites (see above) show the full platform incl. that code (and we have the licence to do so). You can observe the missing view level code and CSS code in action there, and that will help you in case you want to create a functional installation of this software right now.

**Trademarks.** This code still uses the "Epelia" brand name in some places, though this will be phased out eventually. Epelia is a registered trademark of [Micha Gattinger](mailto:mail@michagattinger.de). You are welcome to create and distribute derivative versions of this software, but you have to give your derivative a different name. This means, replace all occurrences of "Epelia" with a different name, replace all occurrences of the Epelia logo with your own logo, and do not give any impression that your derivative version is the official Epelia software or endorsed by it.


## 2. Repository Layout

Roles of the different branches:

* `master` – The open source release of the "standard variant", for application as a marketplace.
* `master+nonfree` – A non-public branch containing the current `master` plus the non-free view layer code that we want to eventually replace.
* `lebensmittelspende+nonfree` – Quick and dirty adaptation of the marketplace software as used on [lebensmittelspende.org](http://lebensmittelspende.org/). With the new backend being developed in branch `backend` and an accompanying frontend repository, this branch will become legacy.
* `backend` – A rework of `master` providing a RESTful JSON backend rather than a PHP+Bootstrap view layer. Will eventually be merged into `master`, and the repository will then be renamed to `food-marketplace-backend`.


## 3. Installation

There is nothing special about it:

1. Create an empty database initialized with [`epelia.sql`](https://github.com/fairdirect/food-marketplace/blob/master/docs/epelia.sql).

2. Configure database access etc. in [`application/configs/application.ini`](https://github.com/fairdirect/food-marketplace/blob/master/application/configs/application.ini).

3. Configure your webserver to serve directory [`public/`](https://github.com/fairdirect/food-marketplace/tree/master/public) as the document root directory of the software.

4. Access the website.

Before providing a platform based on the Epelia software to the public, please update all dependencies. There might be security vulnerabilities in the current state of the software as the dependencies have not been updated in quite some time.


## 4. Usage

TODO
