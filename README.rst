Víctor Hugo Valle Website Parser
================================

What's inside?
--------------

This project comes with some basic and important files to parse a website, and then store some records in the DB:

* IXR - The Incutio XML-RPC Library
* PHP Class Site
* WordPress Client Class


Getting Started
---------------

Create storage directories::

    mkdir files-parsed
    mkdir imgs-parsed

Configure the the variable to parse the site, set the website url in the ``$siteUrl`` variable. For example::

	$siteUrl = 'http://www.sitetobeparsed.net';

Set the absolute route of the file that will be created with the parsed tags. The same should be configured in the variable ``$fileParsed``::

	$fileParsed = '/var/www/parser/file-parsed/myarticles.html';

Create the query with the correct tags that will be parsed. It's necessary the correct writing of the regular expressions like::

	$tags = $xpath->query('//div[@class="columnwrapper"]/div');

The query above is the main one which indicates a block where the articles are wrapped.
::

In the same way keep configuring the query for the titles, contents and images that will be parsed. It's important to bear in mind that the titles, content and images will be taken after the main query.
::

Usage
-----

Execute the parser using php, there's no need to run as sudo. Bear in mind the data base should be already created::

	php parser.php

If the queries were well configured, the data will be saved successfully and the index.php should load all the data in the sample template.

Done!