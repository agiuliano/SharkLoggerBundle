SharkFormLoggerBundle Documentation
=====================================

## Prerequisites

This version of the bundle requires Symfony 2.1 or higher

## Installation

1. Download SharkFormLoggerBundle
2. Enable the Bundle
3. Usage

### 1. Download SharkFormLoggerBundle

**Using composer**

Add the following lines in your composer.json:

```
{
    "require": {
        "shark/formlogger-bundle": "dev-master"
    }
}

```

Now, run the composer to download the bundle:

``` bash
$ php composer.phar update shark/formlogger-bundle
```

### 2. Enable the bundle

Enable the bundle in the kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Shark\FormLoggerBundle\SharkFormLoggerBundle(),
    );
}
```

### 3. Usage

In your forms you can enable the logger simply by setting loggable option to true:

```
public function setDefaultOptions(OptionsResolverInterface $resolver)
{
    $resolver->setDefaults(array(
        'loggable' => true,
    ));
}

public function getName()
{
    return 'your_form_name';
}

```

All your forms with loggable = true option will log all data and errors of your forms.
The logs are available in the app/logs folder. The log filename is the same as the form name (e.g. your_form_name.log)
