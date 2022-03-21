# Sachspendenbörse Marketplace

**[1. Introduction](#1-introduction)**

**[2. Installation](#3-installation)**

**[3. Usage](#4-usage)**


## 1. Introduction

**Overview.** The Sachspendenbörse Marktplace platform is an open source marketplace software specially made for food donations. 

**Online demos.** We operate several online demo and live versions of this software, where you can see it in action:

* [sachspendenbörse.org](http://sachspendenbörse.org) shows the software as a donations in kind marketplace. 

**Changes for the Fairdirect online marketplace- software:**

* A closed area for the procurement of large donations of food items between donors and a registered group (non-profit organizations).

* **Regions.** Currently 105 countries are divided into regions. For all these regions postal codes are stored in the database. Therefore each address can be contacted to a region automatically. This allows for many autonomous regions in one marketplace, all regions are connected to a shopping cart and a checkout. 

* **Specialized on food.** Unlike any other open source marketplace software that we know (like [Fairmondo](https://github.com/fairmondo/fairmondo)), Epelia is specialized software for online food sales. As such, it provides features such as searching for allergene-free products (by allergene), designation of products from controlled organic farming etc..

* A function for placing a request for particular food items in the public area

* Complete market place set up without prices

* Click and Collect as a standard distribution option

* Implementation of the transportation logistics features is under development

* **Offering, ordering and delivering donations of surplus food.** In this case, the marketplace is not used for selling but for utlizing surplus food. Like any marketplace, there can be several parties offering and several parties ordering what is on offer. The development of these features was initiated during the [#WeVsVirus Hackathon](https://wirvsvirushackathon.org/) in March 2020 as project [Tafeln Reorganisation Akquise](https://devpost.com/software/online-lebensmittel-aquise-fur-die-tafeln-fairdirect). 

**Copyright.** Copyright 2020 by the Sachspendenbörse- onlinemarketplace developer. Contributor:

* Micha Gattinger

The Sachspendenbörse- software has been derived from the open source marketplace software "Epelia", which has been released as free software on 2020-03-20 under the AGPL licence. All commits from the Sachspendenbörse- extension up to and including 2020-09-25 are owned by Micha Gattinger. 

The copyrights for [all commits up to and including 2020-02-12 from the "Epelia"- software](https://github.com/Fairdirect/epelia/tree/2af9da356b60f90b79e5900dc883c1184ed32b75) are owned by [Micha Gattinger](mailto:mail@michagattinger.de). 

The copyrights for [all commits up to and including 2020-09-25 from the "Sachspendenbörse"- extension](https://github.com/ifolje/Sachspendenbörse/) are owned by [Micha Gattinger](mailto:mail@michagattinger.de). 

**Licence.** This platform, as available on Github under [Ifolje/Sachspendenbörse](https://github.com/Ifolje/Sachspendenbörse), is free software under the terms of the GNU Affero General Public License v3.0 license, or at your option any later version. For details please consult the licence terms in file [`LICENSE`](https://github.com/fairdirect/food-marketplace/blob/master/LICENSE).

This licence does not cover about 10% additional code that has not yet been published because it contains some third-party non-free code. Before inclusion, it has to be replaced, or thorougly reviewed and partially rewritten. So far, it is contained in the non-public branches with the `+nonfree` suffix. The affected code is some view layer code and the CSS stylesheets.

The unreleased code is necessary for a functional installation. However, our current demo sites (see above) show the full platform incl. that code (and we have the licence to do so). You can observe the missing view level code and CSS code in action there, and that will help you in case you want to create a functional installation of this software right now.

**Trademarks.** This code still uses the "Epelia" brand name in some places, though this will be phased out eventually. Epelia is a registered trademark of [Micha Gattinger](mailto:mail@michagattinger.de). You are welcome to create and distribute derivative versions of this software, but you have to give your derivative a different name. This means, replace all occurrences of "Epelia" with a different name, replace all occurrences of the Epelia logo with your own logo, and do not give any impression that your derivative version is the official Epelia software or endorsed by it.


## 2. Installation

There is nothing special about it:

1. Create an empty database initialized with [`epelia.sql`](https://github.com/fairdirect/food-marketplace/blob/master/docs/epelia.sql).

2. Configure database access etc. in [`application/configs/application.ini`](https://github.com/fairdirect/food-marketplace/blob/master/application/configs/application.ini).

3. Configure your webserver to serve directory [`public/`](https://github.com/fairdirect/food-marketplace/tree/master/public) as the document root directory of the software.

4. Access the website.

Before providing a platform based on the Epelia software to the public, please update all dependencies. There might be security vulnerabilities in the current state of the software as the dependencies have not been updated in quite some time.

