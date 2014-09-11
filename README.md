# TechDivision_Naming

[![Latest Stable Version](https://poser.pugx.org/techdivision/naming/v/stable.png)](https://packagist.org/packages/techdivision/naming) [![Total Downloads](https://poser.pugx.org/techdivision/naming/downloads.png)](https://packagist.org/packages/techdivision/naming) [![Latest Unstable Version](https://poser.pugx.org/techdivision/naming/v/unstable.png)](https://packagist.org/packages/techdivision/naming) [![License](https://poser.pugx.org/techdivision/naming/license.png)](https://packagist.org/packages/techdivision/naming) [![Build Status](https://travis-ci.org/techdivision/TechDivision_Naming.png)](https://travis-ci.org/techdivision/TechDivision_Naming)[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/techdivision/TechDivision_Naming/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/techdivision/TechDivision_Naming/?branch=master)[![Code Coverage](https://scrutinizer-ci.com/g/techdivision/TechDivision_Naming/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/techdivision/TechDivision_Naming/?branch=master)

## Introduction

Naming package implementation providing basic lookup functionality for beans.

## Installation

If you want to use the library with your application you can install it by adding

```sh
{
    "require": {
        "techdivision/naming": "dev-master"
    },
}
```

to your `composer.json` and invoke `composer update` in your project.

## Usage

To lookup a session or message bean, you can create a new instance, inject the actual servlet request/application instance
and invoke the lookup() method by passing the class name of the bean.

```php
// create a new instance and inject the servlet request (necessary for stateful session beans)
$initialContext = new InitialContext();
$initialContext->injectServletRequest($servletRequest);
        
// lookup a session bean
return $initialContext->lookup($proxyClass = 'TechDivision\Example\MySessionBean');
```

# External Links

* Documentation at [appserver.io](http://docs.appserver.io)
* Documentation on [GitHub](https://github.com/techdivision/TechDivision_AppserverDocumentation)
* [Getting started](https://github.com/techdivision/TechDivision_AppserverDocumentation/tree/master/docs/getting-started)
