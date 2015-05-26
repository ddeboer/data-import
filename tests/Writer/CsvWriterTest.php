<?php

namespace Ddeboer\DataImport\Tests\Writer;

use Ddeboer\DataImport\Writer\CsvWriter;

class CsvWriterTest extends StreamWriterTest
{
    public function testWriteItem()
    {
        $writer = new CsvWriter(';', '"', $this->getStream());

        $writer->prepare();
        $writer->writeItem(array('first', 'last'));

        $writer->writeItem(array(
            'first' => 'James',
            'last'  => 'Bond'
        ));

        $writer->writeItem(array(
            'first' => '',
            'last'  => 'Dr. No'
        ));

        $this->assertContentsEquals(
            "first;last\nJames;Bond\n;\"Dr. No\"\n",
            $writer
        );

        $writer->finish();
    }

    public function testWriteUtf8Item()
    {
        $writer = new CsvWriter(';', '"', $this->getStream(), true);

        $writer->prepare();
        $writer->writeItem(array('Précédent', 'Suivant'));

        $this->assertContentsEquals(
            chr(0xEF) . chr(0xBB) . chr(0xBF) . "Précédent;Suivant\n",
            $writer
        );

        $writer->finish();
    }
}
