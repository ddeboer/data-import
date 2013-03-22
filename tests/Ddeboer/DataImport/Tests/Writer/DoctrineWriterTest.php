<?php

namespace Ddeboer\DataImport\Tests\Writer;

use Ddeboer\DataImport\Writer\DoctrineWriter;
use Ddeboer\DataImport\Tests\Fixtures\TestEntity;

class DoctrineWriterTest extends \PHPUnit_Framework_TestCase
{
    public function testWriteItem()
    {
        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
                ->setMethods(array('getRepository', 'getClassMetadata', 'persist'))
                ->disableOriginalConstructor()
                ->getMock();

        $repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
                ->disableOriginalConstructor()
                ->getMock();

        $metadata = $this->getMockBuilder('Doctrine\ORM\Mapping\ClassMetadata')
                ->setMethods(array('getName', 'getFieldNames', 'setFieldValue'))
                ->disableOriginalConstructor()
                ->getMock();

        $metadata->expects($this->any())
                ->method('getName')
                ->will($this->returnValue('Ddeboer\DataImport\Tests\Fixtures\TestEntity'));

        $metadata->expects($this->any())
                ->method('getFieldNames')
                ->will($this->returnValue(array('firstProperty', 'secondProperty')));

        $em->expects($this->once())
                ->method('getRepository')
                ->will($this->returnValue($repo));

        $em->expects($this->once())
                ->method('getClassMetadata')
                ->will($this->returnValue($metadata));

        $em->expects($this->once())
                ->method('persist');

        $writer = new DoctrineWriter($em, 'DdeboerDataImport:TestEntity');

        $item = array(
            'firstProperty' => 'some value',
            'secondProperty'=> 'some other value'
        );
        $writer->writeItem($item);
    }
}
