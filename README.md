About Tropaion
==============
Tropaion is a web-based sport results service developed with current technologies
and best practices in mind. It focuses on usability, web standards and social features.
On top of that it advocates data reuse by making all data available in the semantic web.
The sports data is annotated within the HTML code of the websites 
with [RDFa](http://en.wikipedia.org/wiki/RDFa), using 
the [Sports-Ontology](https://github.com/Tobion/Sports-Ontology) to describe the tournament structure, 
participating teams & athletes, the sports results, and their relationships.

### Name meaning
What do all sports and tournaments have in common? They reward athletes with a trophy (e.g. medal or cup) 
for a specific achievement. The word [*tropaion*](http://en.wikipedia.org/wiki/Tropaion) is the Acient Greek
origin of *trophy*. So I thought this word and a trophy symbol represent a sports results service best.

License
-------
Tropaion is Open Source Software, and licensed under the [GNU GPLv3](http://www.gnu.org/licenses/gpl.html) 
(see [LICENSE.md](https://github.com/Tobion/Tropaion/blob/master/LICENSE.md)). 
The software provides it's data under the terms of the 
[Creative Commons Attribution 3.0 Unported License](http://creativecommons.org/licenses/by/3.0/).
So the service and the content conforms to the [Open Definition](http://opendefinition.org/) 
provided by the [Open Knowledge Foundation](http://okfn.org/).

System Requirements
-------------------
- Web Server (like Apache)
- PHP 5.3.2 or higher
- MySQL 5 or higher

Feedback
--------
Bug reports and feature request can be made under <https://github.com/Tobion/Tropaion/issues>.

Contributing
------------
Want to contribute to the development of Tropaion? Great! This is what makes open source
unique. 

### Basics
Tropaion is written in PHP and based on the [Symfony2 framework](http://symfony.com). 
You can read the [documentation](http://symfony.com/doc/current/) to understand its fundamentals. 
Tropaion uses Git as distributed revision control system and it's development page
is hosted at GitHub under <https://github.com/Tobion/Tropaion>.

### Configure your development environment
1. [Set up Git](http://help.github.com/set-up-git-redirect)
2. Fork this project ([see example how to do it](http://help.github.com/fork-a-repo/))
3. Install a local web server with PHP and MySQL; I recommend [XAMPP](http://www.apachefriends.org/)
4. Configure Apache host by adding the following lines to the file `/xampp/apache/conf/httpd.conf`
	(replace PATH-TO with your path to the project folder); (re)start Apache and MySQL

		Alias /tropaion "PATH-TO/Tropaion/web/"
		<Directory "PATH-TO/Tropaion/web/">
				Options Indexes FollowSymLinks Includes ExecCGI
				AllowOverride All
				Order allow,deny
				Allow from all
		</Directory>

5. Using the Git-Bash, execute the following command inside the Tropaion folder to
	install third-party dependencies and to publish the bundle's assets 

		./bin/vendors install
		./app/console assets:install --symlink

6. Open your browser at http://localhost/tropaion/config.php to check the system requirements
	and configure your database connection
7. Access the starting page of Tropaion at http://localhost/tropaion/app_dev.php
8. Edit the code with whatever text editor or IDE you prefer; 
	I recommend [NetBeans](http://netbeans.org) 
	with [Twig Plugin](http://plugins.netbeans.org/plugin/37069/php-twig)
9. [Send pull requests](http://help.github.com/send-pull-requests/) to let me 
	know about the changes you've made

TODO
----
- make database dump available without any private information
- describe database initialisation including stored procedures (add command for it)