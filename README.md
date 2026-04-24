Contao-Bootstrap Accordion
======================

[![Version](http://img.shields.io/packagist/v/contao-bootstrap/accordion.svg?style=for-the-badge&label=Latest)](http://packagist.org/packages/contao-bootstrap/accordion)
[![GitHub issues](https://img.shields.io/github/issues/contao-bootstrap/accordion.svg?style=for-the-badge&logo=github)](https://github.com/contao-bootstrap/accordion/issues)
[![License](http://img.shields.io/packagist/l/contao-bootstrap/accordion.svg?style=for-the-badge&label=License)](http://packagist.org/packages/contao-bootstrap/accordion)
[![Build Status](https://img.shields.io/github/workflow/status/contao-bootstrap/accordion/contao-bootrap-accordion?logo=githubactions&logoColor=%23fff&style=for-the-badge)](https://github.com/contao-bootstrap/accordion/actions)
[![Downloads](http://img.shields.io/packagist/dt/contao-bootstrap/accordion.svg?style=for-the-badge&label=Downloads)](http://packagist.org/packages/contao-bootstrap/accordion)

This extension provides Bootstrap integration into Contao.

Contao-Bootstrap is a modular integration. This extension provides the bootstrap accordion into Contao. It uses the default
accordion element of Contao and extends it with an accordeon group element.


Changelog
---------

See [changelog](CHANGELOG.md)


Requirements
------------

 - PHP ^8.1
 - Contao ^4.13 || ^5.0


Install
-------

### Managed edition

When using the managed edition it's pretty simple to install the package. Just search for the package in the
Contao Manager and install it. Alternatively you can use the CLI.

```bash
# Using the contao manager
$ php contao-manager.phar.php composer require contao-bootstrap/accordion~2.0

# Using composer directly
$ php composer.phar require contao-bootstrap/accordion~2.0

```

### Symfony application

If you use Contao in a symfony application without contao/manager-bundle, you have to register following bundles
manually:

```php

class AppKernel
{
    public function registerBundles()
    {
        $bundles = [
            // ...
            new \ContaoCommunityAlliance\MetaPalettes\CcaMetaPalettesBundle(),
            new Netzmacht\Contao\Toolkit\Bundle\NetzmachtContaoToolkitBundle(),
            new ContaoBootstrap\Core\ContaoBootstrapCoreBundle(),
            new ContaoBootstrap\Grid\ContaoBootstrapAccordionBundle()
        ];
    }
}

```

Migration
-------

To automatically migrate your accordion from Start- and Stop-Wrappers to nested fragments, you have to enable the
migration by creating `config/packages/contao_bootstrap_accordion.yaml` with the following content:

```yaml
contao_bootstrap_accordion:
    enable_wrapper_migration: true
```

Afterwards you can run the migration in the Contao Manager or via CLI.
