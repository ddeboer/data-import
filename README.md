Ddeboer Data Import library
===========================
[![Build Status](https://travis-ci.org/ddeboer/data-import.png?branch=master)](https://travis-ci.org/ddeboer/data-import) [![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/ddeboer/data-import/badges/quality-score.png?s=41129c80140adc6931288c9df15fb87ec6ea6f8a)](https://scrutinizer-ci.com/g/ddeboer/data-import/) [![Code Coverage](https://scrutinizer-ci.com/g/ddeboer/data-import/badges/coverage.png?s=724267091a6d02f83b6c435a431e71d467b361f8)](https://scrutinizer-ci.com/g/ddeboer/data-import/) [![Latest Stable Version](https://poser.pugx.org/ddeboer/data-import/v/stable.png)](https://packagist.org/packages/ddeboer/data-import)

Introduction
------------
This PHP library offers a way to read data from, and write data to, a range of
file formats and media. Additionally, it includes tools to manipulate your data.

Features
--------

* Read from and write to CSV files, Excel files, databases, and more.
* Convert between charsets, dates, strings and objects on the fly.
* Build reusable and extensible import workflows.
* Decoupled components that you can use on their own, such as a CSV reader and writer.
* Well-tested code.

Installation
------------

This library is available on [Packagist](http://packagist.org/packages/ddeboer/data-import).
The recommended way to install it is through [Composer](http://getcomposer.org):

```bash
$ composer require ddeboer/data-import:@stable
```

For integration with Symfony2 projects, the [DdeboerDataImportBundle](https://github.com/ddeboer/DdeboerDataImportBundle)
is available.

Usage
-----

### The workflow

Each data import revolves around the workflow and takes place along the following lines:

1. Construct a [reader](#readers).
2. Construct a workflow and pass the reader to it. Add at least one [writer](#writers) to
   the workflow.
3. Optionally, add [filters](#filters), item converters and [value converters](#value-converters) to the
   workflow.
4. Process the workflow. This will read the data from the reader, filter and
   convert the data, and write the output to each of the writers.

So, schematically:

```php
use Ddeboer\DataImport\Workflow;
use Ddeboer\DataImport\Reader;
use Ddeboer\DataImport\Writer;
use Ddeboer\DataImport\Filter;

$reader = new Reader\...;
$workflow = new Workflow($reader);
$workflow
    ->addWriter(new Writer\...())
    ->addWriter(new Writer\...())
    ->addFilter(new Filter\CallbackFilter(...))
    ->process()
;
```

### Readers

This library includes a handful of readers:

* An `ArrayReader` for reading arrays (or testing your workflow).
* A `CsvReader` for reading CSV files, optimized to use as little memory as possible:
    ```php
    use Ddeboer\DataImport\Reader\CsvReader;

    $reader = new CsvReader(new \SplFileObject('/path/to/csv_file.csv'));
    ```
    **Note:** This reader operates in a 'strict mode' by default, see [CsvReader strict mode](#csvreader-strict-mode) for more details.
* A `DbalReader` to read data through [Doctrine’s DBAL](http://www.doctrine-project.org/projects/dbal.html):
    ```php
    use Ddeboer\DataImport\Reader\DbalReader;

    $reader = new DbalReader(
        $connection, // Instance of \Doctrine\DBAL\Connection
        'SELECT u.id, u.username, g.name FROM `user` u INNER JOIN groups g ON u.group_id = g.id'
    );
    ```
* A `DoctrineReader` to read data through the [Doctrine ORM](http://www.doctrine-project.org/projects/orm.html):
    ```php
    use Ddeboer\DataImport\Reader\DoctrineReader;

    $reader = new DoctrineReader($entityManager, 'Your\Namespace\Entity\User');
    ```
* An `ExcelReader` that acts as an adapter for the [PHPExcel library](http://phpexcel.codeplex.com/):
    ```php
    use Ddeboer\DataImport\Reader\ExcelReader;

    $reader = new ExcelReader(new \SplFileObject('/path/to/ecxel_file.xls'));
    ```
* You can create your own data reader by implementing the [ReaderInterface](/src/Ddeboer/DataImport/Reader/ReaderInterface.php).

After you’ve set up your reader, construct the workflow from it:
```php
$workflow = new Workflow($reader);
```

### Writers

Many of the data writers closely resemble their reader counterparts:
* An `ArrayWriter`.
* A `CsvWriter`.
* A `DoctrineWriter`.

Also available are:
* A `ConsoleProgressWriter` that displays import progress when you start the
  workflow from the command-line:
    ```
    use Ddeboer\DataImport\Writer\ConsoleProgressWriter;
    use Symfony\Component\Console\Output\ConsoleOutput;

    $output = new ConsoleOutput(...);
    $progressWriter = new ConsoleProgressWriter($output, $reader);
    ```

Build your own writer by implementing the [WriterInterface](/src/Ddeboer/DataImport/Reader/WriterInterface.php).

If you want, you can use multiple writers:

```php
$workflow
    ->addWriter($progressWriter)
    ->addWriter($csvWriter)
;
```

### Filters

A filter decides whether data input is accepted into the import process. 

#### [CallbackFilter](/src/Ddeboer/DataImport/Filter/CallBackFilter.php)

```php
use Ddeboer\DataImport\Filter\CallbackFilter;

// Don’t import The Beatles
$filter = new CallbackFilter(function ($data) {
    if ('The Beatles' == $data['name']) {
        return false;
    } else {
        return true;
    }
});

$workflow->addFilter($filter);
```

#### [OffsetFilter](/src/Ddeboer/DataImport/Filter/OffsetFilter.php)

OffsetFilter allows you to

* skip certain amount of items from the beginning
* process only specified amount of items (and skip the rest)

You can combine these two parameters to process a slice from the middle of the
data: like rows 5-7 of a CSV file with ten rows.

OffsetFilter is configured by it's constructor:
`new OffsetFilter($offset = 0, $limit = null)`. Note: `$offset` is a 0-based index.

```php
use Ddeboer\DataImport\Filter\OffsetFilter;

// Default implementation is to start from the beginning without maximum count
$filter = new OffsetFilter(0, null);
$filter = new OffsetFilter(); // You can omit both parameters

// Start from the third item, process to the end
$filter = new OffsetFilter(2, null);
$filter = new OffsetFilter(2); // You can omit the second parameter

// Start from the first item, process max three items
$filter = new OffsetFilter(0, 3);

// Start from the third item, process max five items (items 3 - 7)
$filter = new OffsetFilter(2, 5);
```

#### [ValidatorFilter](/src/Ddeboer/DataImport/Filter/ValidatorFilter.php)

Its a common use case to validate the data before you save it to the database.
Exactly for this use case we created the ValidatorFilter. See how it works:

```php

$filter = new ValidatorFilter($validator);
$filter->add('email', new Assert\Email());
$filter->add('sku', new Assert\NotBlank());
```

The default behaviour for the validator is to collect all violations and skip
each row which isn't valid. If you want to stop on the first failing row you can
call `ValidatorFilter::throwExceptions()`. 
Now the filter will throw a [ValidationException](/src/Ddeboer/Exception/ValidationException] 
which contains the line number and the violation list.

_Note_
Its recommend to add the ValidatorFilter before you add all other filters.
For a detailed explanation see https://github.com/ddeboer/data-import/pull/47#issuecomment-31969949.

### Item converters

### Value converters

Value converters are used to convert specific fields (e.g., columns in database).

* A `DateTimeValueConverter` that converts a date representation in a format you
  specify into a `DateTime` object:
      ```php
      use Ddeboer\DataImport\ValueConverter\DateTimeValueConverter;

      $converter = new DateTimeValueConverter('d/m/Y H:i:s');
      $workflow->addValueConverter('my_date_field', $converter);
      ```

* A `StringToObjectConverter` that looks up an object in the database based
  on a string value:
    ```php
    use Ddeboer\DataImport\ValueConverter\StringToObjectConverter;

    $converter = new StringToObjectConverter($repository, 'name');
    $workflow->addValueConverter('input_name', $converter);
    ```

An example
----------

```php
use Ddeboer\DataImport\Workflow;
use Ddeboer\DataImport\Source\HttpSource;
use Ddeboer\DataImport\Source\Filter\Unzip;
use Ddeboer\DataImport\Reader\CsvReader;
use Ddeboer\DataImport\ValueConverter\DateTimeValueConverter;
use Ddeboer\DataImport\ValueConverter\CallbackValueConverter;
use Ddeboer\DataImport\Writer\CallbackWriter;

(...)

// Create the source; here we use an HTTP one
$source = new HttpSource('http://www.opta.nl/download/registers/nummers_csv.zip');

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
    ->addValueConverter('twn_nummertm', new CallbackValueConverter(function ($input) {
        return str_replace('-', '', $input);
    }))
    ->addValueConverter('twn_nummervan', new CallbackValueConverter(function ($input) {
        return str_replace('-', '', $input);
    }))

    // Use one of the writers supplied with this bundle, implement your own, or use a closure:
    ->addWriter(new CallbackWriter(function ($csvLine) {
        var_dump($csvLine);
    }))
;

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
            )
        )
    );

    // ...
    // create the workflow and reader etc.
    // ...

    $workflow->addValueConverter(new ArrayValueConverterMap(array(
        'name' => array(new CharsetValueConverter('UTF-8', 'UTF-16')), // encode to UTF-8
        'price' => array(new CallbackValueConverter(function ($input) {
            return str_replace('€', '', $intput); // remove € char
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
            )
        )
    );
```

GlobalMapping
-------------

The global-mapping allows you to define an array that is used to rename fields of an item.

Using global-mapping can be used to add renaming-rules for a multi-level array and is applied
after the standard-mapping rules are applied.

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
    $workflow->addMapping('baz', 'bazinga');

    // this defines renaming global rules
    $workflow->setGlobalMapping(array(
        'foo' => 'fooloo',

        // we need to use the new name here because global mapping is applied after standard mapping
        'bazinga' => array(
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

Troubleshooting
---------------

### CsvReader strict mode

The `CsvReader` operates in 'strict mode' by default, this means that if there
are any rows in the CSV provided that contain a different number of values than
the column headers provided, then an error is logged and the row is skipped.

To disable this functionality, you can set `$reader->setStrict(false)` after
you instantiate the reader.

Disabling strict mode means:

1. Any rows that contain fewer values than the column headers are simply
   padded with null values.
2. Any additional values in a row that contain more values than the
   column headers are ignored.

Examples where this is useful:

- **Outlook 2010:** which omits trailing blank values
- **Google Contacts:** which exports more values than there are column headers
