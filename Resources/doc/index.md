SharkFormLoggerBundle Documentation
=====================================

SharkFormLoggerBundle allows you to log form errors and data

##Log your forms
**Suppose your form looks like this**

``` php

class SimpleForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('field', 'text');
    }

    public function getName()
    {
        return 'simple_form';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Your\Entity\Namespace',
            'loggable' => true
        ));
    }
}
```
_This example suppose that 'field' has NotBlank validator_

When your form was submitted, SharkFormLoggerBundle generates for you a log file that contains the form's fields and eventually its errors:

```
// app/logs/simple_form.log

[2013-03-01 10:26:51] shark.form.ERROR: [8gp7fpe7] : [field] => '' [Errors: Required] [] []
```
*8gp7fpe7* is a key that identifies the user that submitted the form 




## Prerequisites

This version of the bundle requires Symfony 2.2

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
        "shark/formlogger-bundle": "2.2.*"
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

```php
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
