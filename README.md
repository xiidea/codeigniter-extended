Codeigniter2 Extended Edition!
=============================

Welcome to the Codeigniter Extended Edition - a fully-functional Codeigniter2
application that you can use as the skeleton for your new applications.

This document contains information on how to download, install, and start
using Codeigniter2 Extended Edition.

1) Installing the Extended Edition
----------------------------------

### Use Composer

[Download](https://github.com/Xiidea/cix/archive/master.zip) and extract or Clone this project first. Go to downloaded directory.

If you don't have [Composer][1] yet, download it following the instructions on
http://getcomposer.org/ or just run the following command:

    curl -s http://getcomposer.org/installer | php

If you want some [customization](./docs/customization.md), first make it in the composer.json file. Then, use the `install` command to download all dependencies along with codeigniter framework.

    php composer.phar install

if you like to install with all the default configuration settings you may just run the following command

    php composer.phar create-project xiidea/codeigniter-extended path/ 1.2.1

Composer will install Codeigniter2 and all the dependencies under the working directory. And ask you for some configuration values.


2) Browsing the Demo Application
--------------------------------

Congratulations! You're now ready to use Codeigniter2.

Edit the files with your preferences (domain, languages, database, authentication):

- {application}/config/config.php
- {application}/config/database.php
- {application}/config/ez_rbac.php

Create a virtualhost setting the document root pointing to /path/of/web-root directory

	<VirtualHost *:80>
		ServerName mydomain.com
		ServerAlias www.mydomain.com
		DocumentRoot /path/to/web
	</VirtualHost>


##Backend user and password

The default user to access to the private zone is:

    user: 		admin@admin.com  
    password: 	123456


3) Getting started with Codeigniter2 Extended Edition
----------------------------------------------------

This distribution is meant to be the starting point for your Codeigniter2
applications, but it also contains some sample code that you can learn from
and play with.


What's inside?
---------------

The Codeigniter2 Extended Edition is configured with the following defaults:

  * Twig as template engine(if you chose to add it);

  * Swiftmailer is configured(if you chose to add it);


It comes pre-configured with the following libraries:

  * [EzRbac][2] Role Based Access Control Library

  * [CI_Base_Model][3] An extension of CodeIgniter's base Model class

  * Enhanced [Controller](./docs/controller.md) Library

  * Enhanced Loader Library(For support twig template engine  and basic layout)

  * CIX [Twig Extension](./docs/twig.md) Library

  * Enhanced Language Library(gettext localization implementation)

  * You can use the [PHP built-in web server](./docs/server.md) to run CIX application

  * JS/CSS Minifier. you can use (<code>assets/css/mini.php?files=file1,file2</code> and <code>assets/js/mini.php?files=file1,file2</code>)   
    or (<code>assets/css/file1,file2,file3.css</code> and <code>assets/js/file1,file2.js</code> with rewrite enable)


Enjoy!

[1]:  http://getcomposer.org/
[2]:  http://xiidea.github.io/ezRbac
[3]:  http://ronisaha.github.io/ci-base-model/
