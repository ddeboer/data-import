[![Build Status](https://travis-ci.org/ddeboer/data-import.png?branch=master)](https://travis-ci.org/ddeboer/data-import)

Ddeboer Data Import library
===========================

Introduction
------------
This PHP 5.3 library offers a way to read data from, and write data to, a
range or file formats and media. During the import, you can also manipulate
your data with filters and converters.

Installation
------------

This library is available on [Packagist](http://packagist.org/packages/ddeboer/data-import). 
The recommended way to install Ddeboer Data Import is [through composer](http://getcomposer.org).

To install it, add the following to your `composer.json`:

```JSON
{
    "require": {
        ...
        "ddeboer/data-import": "dev-master",
        ...
    }
}
```

And run `$ php composer.phar install`.

If you want to use this library in a Symfony2 project, you may choose to use
the [DdeboerDataImportBundle](https://github.com/ddeboer/DdeboerDataImportBundle)
instead.

Usage
-----

1. Create an `splFileObject` from the source data. You can also use a `source`
   object to retrieve this `splFileObject`: construct a source and add `source
   filters`, if you like.
2. Construct a `reader` object and pass an `splFileObject` to it.
3. Construct a `workflow` object and pass the reader to it. Add at least one
   `writer` object to this workflow. You can also add `filters` and `converters`
   to the workflow.
4. Process the workflow: this will read the data from the reader, filter and
   convert the data, and write it to the writer(s).

An example:

```php
use Ddeboer\DataImport\Workflow;
use Ddeboer\DataImport\Source\Http;
use Ddeboer\DataImport\Source\Filter\Unzip;
use Ddeboer\DataImport\Reader\CsvReader;
use Ddeboer\DataImport\ValueConverter\DateTimeValueConverter;

(...)

// Create the source; here we use an HTTP one
$source = new Http('http://www.opta.nl/download/registers/nummers_csv.zip');

// As the source file is zipped, we add an unzip filter
$source->addFilter(new Unzip('nummers.csv'));

// Retrieve the \SplFileObject from the source
$file = $source->getFile();

// Create and configure the reader
$csvReader = new CsvReader($file);
$csvReader->setHeaderRowNumber(0);

// Create the workflow
$workflow = new Workflow($csvReader);
$dateTimeConverter = new DateTimeValueConverter();

// Add converters to the workflow
$workflow
    ->addValueConverter('twn_datumbeschikking', $dateTimeConverter)
    ->addValueConverter('twn_datumeind', $dateTimeConverter)
    ->addValueConverter('datummutatie', $dateTimeConverter)

// You can also add closures as converters
    ->addValueConverter('twn_nummertm',
        new \Ddeboer\DataImport\ValueConverter\CallbackValueConverter(
            function($input) {
                return str_replace('-', '', $input);
            }
        )
    ->addValueConverter('twn_nummervan',
        new \Ddeboer\DataImport\ValueConverter\CallbackValueConverter(
            function($input) {
                return str_replace('-', '', $input);
            }
        )

// Use one of the writers supplied with this bundle, implement your own, or use
// a closure:
    ->addWriter(new \Ddeboer\DataImport\Writer\CallbackWriter(
        function($csvLine) {
            var_dump($csvLine);
        }
    ));

// Process the workflow
$workflow->process();
```

ArrayValueConverterMap
----------------------

The ArrayValueConverterMap is used to filter values of a multi-level array.

The converters defined in the list are applied on every data-item's value that match the defined array_keys.

```php

    //...
    $data = array(
        'products' => array(
            0 => array(
                'name' => 'some name',
                'price' => '€12,16',
            ),
            1 => array(
                'name' => 'some name',
                'price' => '€12,16',
            ),
        )
    );

    // ...
    // create the workflow and reader etc.
    // ...

    $workflow->addValueConverter(new ArrayValueConverterMap(array(
        'name' => array(new CharsetValueConverter('UTF-8', 'UTF-16')),  // encode to UTF-8
        'price' => array(new CallbackValueConverter(function($input) {  // remove € char
            return str_replace('€', '', $intput);
        }),
    )));

    // ..
    // after filtering data looks as follows
    $data = array(
        'products' => array(
            0 => array(
                'name' => 'some name', // in UTF-8
                'price' => '12,16',
            ),
            1 => array(
                'name' => 'some name',
                'price' => '12,16',
            ),
        )
    );

```

GlobalMapping
-------------

The global-mapping allows you to define an array that is used to rename fields of an item.

Using global-mapping can be used to add renaming-rules for a multi-level array and is applied after the standard-mapping rules are applied.

```php

    //...
    $data = array(
        0 => array(
            'foo' => 'bar',
            'baz' => array(
                'some' => 'value',
                'some2' => 'value'
            )
        )
    );

    // ...
    // create the workflow and reader etc.
    // ...

    // this defines a single mapping
    $workflow->addMapping('baz' => 'bazinga');

    // this defines renaming global rules
    $workflow->setGlobalMapping(array(
        'foo' => 'fooloo',
        'bazinga' => array( // we need to use the new name here because global mapping is applied after standard mapping
            'some' => 'something',
            'some2' => 'somethingelse'
        )
    ));

    // ..
    // after filtering data looks as follows
    $data = array(
        0 => array(
            'fooloo' => 'bar',
            'bazinga' => array(
                'something' => 'value',
                'somethingelse' => 'value'
            )
        )
    );
```

