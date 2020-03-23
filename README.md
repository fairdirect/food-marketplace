# Food Donation Platform

## 1. Introduction

**Overview.** The Fairdirect e.V. Food Donation Platform is an open source marketplace platform geared towards offering and ordering unsellable food. We provide an online version of this software at [lebensmittelspende.org](http://lebensmittelspende.org/). As of 2020-03-22, this is a demo version, but it will be replaced with a production version in due time. Please [contact us by e-mail](mailto:mail@fairdirect.org) to get access to the backend functionality of the demo version.


**Features.**

* **Marketplace.** Users get a single user account and can use it to order food donations from any and all of the donors on the platform. This is similar to eBay, Amazon and other e-commerce platforms that provie multiple shops on a single platform, just that this marketplace is about donating unsellable items, not selling them.

* **Specialized on food.** Unlike any other open source marketplace software that we know (like [Fairmondo](https://github.com/fairmondo/fairmondo)), this one is specialized for food products. As such, it provides features such as searching for allergene-free products (by allergene), designation of products from controlled organic farming etc..

* **Features for offering, ordering and delivering food donations.** These are the features that make this marketplace platform different from any marketplace with shops and sales. Most of these features are still in planning stage, and you can find a good overview of our plans in [our DevPost project](https://devpost.com/software/online-lebensmittel-aquise-fur-die-tafeln-fairdirect).


**Technology.** This software is written in PHP, using the Zend Framework and the Bootstrap CSS framework. It uses PostgreSQL as its database.


**Copyright.** Copyright 2014-2020 by the developers. List of contributors:

* Sebastian Hösel. Special thanks for his contributions in writing the original marketplace software.
* Christian Beck
* David Goraj
* Matthias Ansorg

This software is forked from the open source marketplace software "Epelia", which has been released as free software on 2020-03-20 under the AGPL licence. Due to this history, the copyrights for [all commits up to and including 2020-02-12](https://github.com/Fairdirect/epelia/tree/2af9da356b60f90b79e5900dc883c1184ed32b75) are owned by [Micha Gattinger](mailto:mail@michagattinger.de). Parts of the code in these commits has been developed by other develoeprs, but all copyrights has been transferred to Micha Gattinger for these contributions.

**State of completeness.** So far only about 90% of the code required for a fully functional installation is contained in this repository. The remaining code, namely some view layer code and the CSS stylesheets, has to be sorted through and tidied up by us at a convenient time before its release as free software. It may contain copyrighted material that we have not the rights to publish open source, so we have to rewrite those parts first. However, the current demo site lebensmittelspende.org shows the full platform incl. that code, and you can observe the missing view level code and CSS code in action there, which will help you in case you want to create a functional installation of this software right now by yourself.


## 2. Installation

There is nothing special about it:

1. Create an empty database initialized with [`epelia.sql`](https://github.com/fairdirect/food-donation-platform/blob/master/docs/epelia.sql).

2. Configure database access etc. in [`application/configs/application.ini`](https://github.com/fairdirect/food-donation-platform/blob/master/application/configs/application.ini).

3. Configure your webserver to serve directory [`public/`](https://github.com/fairdirect/food-donation-platform/tree/master/public) as the document root directory of the software.

4. Access the website.

Before providing a platform based on this software to the public, please update all dependencies. There might be security vulnerabilities in the current state of the software as the dependencies have not been updated in quite some time.


## 3. Usage

TODO