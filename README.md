PHP CodeSniffer for CakePHP
=========================

This repository contains the CakePHP standard for use with the pear package [PHP CodeSniffer](http://pear.php.net/package/PHP_CodeSniffer)

# Installation

Obviously, you need [PHP CodeSniffer](http://pear.php.net/package/PHP_CodeSniffer/download) for this repository to have any meaning.

Locate the `CodeSniffer/Standards` folder in your CodeSniffer install, and install this repository with the name "Cake" e.g.:

    cd <PEAR>/PHP/CodeSniffer/Standards/
    git clone git://github.com/evilbloodydemon/Cake_PHP_CodeSniffer.git Cake

If you work mainly with CakePHP, or your work simply follows the same coding standards, you may wish to configure PHP Code Sniffer to use this standard by default:

    phpcs --config-set default_standard Cake

# Use as a git pre-commit hook

To run `phpcs` checks each time you commit a file, you can use the pre-commit hook provided in this repo. For example

    (linux)
    cd /var/www/apps/myapp/.git/hooks/
    cp /usr/share/pear/PHP/CodeSniffer/Standards/Cake/pre-commit .
    chmod +x pre-commit

    (windows)
    cd <YOUR_PROJECT/.git/hooks
    mklink pre-commit <PEAR>\PHP\CodeSniffer\Standards\Cake\pre-commit

# References and Credits

* [http://lifeisbetter.in/blog/2010/08/09/yet-another-version-of-php-codesniffer-for-cakephp](http://lifeisbetter.in/blog/2010/08/09/yet-another-version-of-php-codesniffer-for-cakephp)
* [https://github.com/venkatrs/Cake_PHP_CodeSniffer](https://github.com/venkatrs/Cake_PHP_CodeSniffer)
* [https://github.com/AD7six/cakephp-codesniffs](https://github.com/AD7six/cakephp-codesniffs)
* [http://www.sanisoft.com/downloads/cakephp_sniffs](http://www.sanisoft.com/downloads/cakephp_sniffs)
