<?php

namespace Ddeboer\DataImport\Tests\Source\Filter;

use Ddeboer\DataImport\Source\Filter\NormalizeLineBreaks;

class NormalizeLineBreaksTest extends \PHPUnit_Framework_TestCase
{
    public function testFilter()
    {
        $file = new \SplFileObject(__DIR__.'/../../Fixtures/data_cr_breaks.csv');

        $count = 0;
        foreach ($file as $line) {
            $count++;
        }

        // Two lines but has wrong line break
        $this->assertEquals(1, $count, 'File contains more lines, but \SplFileObject only recognizes LF breaks');

        $filter = new NormalizeLineBreaks();
        $newFile = $filter->filter($file);

        $count = 0;
        foreach ($newFile as $line) {
            $count++;
        }

        $this->assertEquals(3, $count, 'Now file should contain 3 LF lines');
    }
}