<?php

namespace Ddeboer\DataImport\Tests\Writer;

use Ddeboer\DataImport\Writer\DoctrineWriter;
use Ddeboer\DataImport\Tests\Fixtures\Entity\TestEntity;
use Ddeboer\DataImport\Tests\Fixtures\Document\TestDocument;

class DoctrineWriterTest extends \PHPUnit_Framework_TestCase
{
    public function testWriteItem()
    {
        $em = $this->getEntityManager();

        $em->expects($this->once())
                ->method('persist');

        $writer = new DoctrineWriter($em, 'DdeboerDataImport:TestEntity');

        $association = new TestEntity();
        $item = array(
            'firstProperty'   => 'some value',
            'secondProperty'  => 'some other value',
            'firstAssociation'=> $association
        );
        $writer->writeItem($item);
    }

    public function testEntityBatches()
    {
        $em = $this->getEntityManager();
        $em->expects($this->exactly(11))
            ->method('persist');

        $em->expects($this->exactly(4))
            ->method('flush');

        $writer = new DoctrineWriter($em, 'DdeboerDataImport:TestEntity');
        $writer->prepare();

        $writer->setBatchSize(3);
        $this->assertEquals(3, $writer->getBatchSize());

        $association = new TestEntity();
        $item = array(
            'firstProperty'   => 'some value',
            'secondProperty'  => 'some other value',
            'firstAssociation'=> $association
        );

        for ($i = 0; $i < 11; $i++) {
            $writer->writeItem($item);
        }

        $writer->finish();
    }

    public function testDocumentBatches()
    {
        $dm = $this->getMongoDocumentManager();
        $dm->expects($this->exactly(11))
            ->method('persist');

        $dm->expects($this->exactly(4))
            ->method('flush');

        $writer = new DoctrineWriter($dm, 'DdeboerDataImport:TestDocument');
        $writer->prepare();

        $writer->setBatchSize(3);
        $this->assertEquals(3, $writer->getBatchSize());

        $association = new TestDocument();
        $item = array(
            'firstProperty'   => 'some value',
            'secondProperty'  => 'some other value',
            'firstAssociation'=> $association
        );

        for ($i = 0; $i < 11; $i++) {
            $writer->writeItem($item);
        }

        $writer->finish();
    }

    public function testUnsupportedDatabaseException()
    {
        $this->setExpectedException('Ddeboer\DataImport\Exception\UnsupportedDatabaseTypeException');
        $om = $this->getMock('Doctrine\Common\Persistence\ObjectManager');
        $writer = new DoctrineWriter($om, 'DdeboerDataImport:TestDocument');
        $writer->prepare();
    }

    protected function getEntityManager()
    {
        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->setMethods(array('getRepository', 'getClassMetadata', 'persist', 'flush', 'clear', 'getConnection'))
            ->disableOriginalConstructor()
            ->getMock();

        $repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $metadata = $this->getMockBuilder('Doctrine\ORM\Mapping\ClassMetadata')
            ->setMethods(array('getName', 'getFieldNames', 'getAssociationNames', 'setFieldValue'))
            ->disableOriginalConstructor()
            ->getMock();

        $metadata->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('Ddeboer\DataImport\Tests\Fixtures\Entity\TestEntity'));

        $metadata->expects($this->any())
            ->method('getFieldNames')
            ->will($this->returnValue(array('firstProperty', 'secondProperty')));

        $metadata->expects($this->any())
            ->method('getAssociationNames')
            ->will($this->returnValue(array('firstAssociation')));

        $configuration = $this->getMockBuilder('Doctrine\DBAL\Configuration')
            ->setMethods(array('getConnection'))
            ->disableOriginalConstructor()
            ->getMock();

        $connection = $this->getMockBuilder('Doctrine\DBAL\Connection')
            ->setMethods(array('getConfiguration', 'getDatabasePlatform', 'getTruncateTableSQL', 'executeQuery'))
            ->disableOriginalConstructor()
            ->getMock();

        $connection->expects($this->any())
            ->method('getConfiguration')
            ->will($this->returnValue($configuration));

        $connection->expects($this->any())
            ->method('getDatabasePlatform')
            ->will($this->returnSelf());

        $connection->expects($this->any())
            ->method('getTruncateTableSQL')
            ->will($this->returnValue('TRUNCATE SQL'));

        $connection->expects($this->any())
            ->method('executeQuery')
            ->with('TRUNCATE SQL');

        $em->expects($this->once())
            ->method('getRepository')
            ->will($this->returnValue($repo));

        $em->expects($this->once())
            ->method('getClassMetadata')
            ->will($this->returnValue($metadata));

        $em->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue($connection));

        $self = $this;
        $em->expects($this->any())
            ->method('persist')
            ->will($this->returnCallback(function ($argument) use ($self) {
                $self->assertNotNull($argument->getFirstAssociation());
                return true;
            }));

        return $em;
    }

    protected function getMongoDocumentManager()
    {
        $dm = $this->getMockBuilder('Doctrine\ODM\MongoDB\DocumentManager')
            ->setMethods(array('getRepository', 'getClassMetadata', 'persist', 'flush', 'clear', 'getConnection', 'getDocumentCollection'))
            ->disableOriginalConstructor()
            ->getMock();

        $repo = $this->getMockBuilder('Doctrine\ODM\MongoDB\DocumentRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $metadata = $this->getMockBuilder('Doctrine\ODM\MongoDB\Mapping\ClassMetadata')
            ->setMethods(array('getName', 'getFieldNames', 'getAssociationNames', 'setFieldValue'))
            ->disableOriginalConstructor()
            ->getMock();

        $metadata->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('Ddeboer\DataImport\Tests\Fixtures\Document\TestDocument'));

        $metadata->expects($this->any())
            ->method('getFieldNames')
            ->will($this->returnValue(array('firstProperty', 'secondProperty')));

        $metadata->expects($this->any())
            ->method('getAssociationNames')
            ->will($this->returnValue(array('firstAssociation')));

        $configuration = $this->getMockBuilder('Doctrine\DBAL\Configuration')
            ->setMethods(array('getConnection'))
            ->disableOriginalConstructor()
            ->getMock();

        $connection = $this->getMockBuilder('Doctrine\DBAL\Connection')
            ->setMethods(array('getConfiguration', 'getDatabasePlatform', 'getTruncateTableSQL', 'executeQuery'))
            ->disableOriginalConstructor()
            ->getMock();

        $connection->expects($this->any())
            ->method('getConfiguration')
            ->will($this->returnValue($configuration));

        $connection->expects($this->any())
            ->method('getDatabasePlatform')
            ->will($this->returnSelf());

        $connection->expects($this->never())
            ->method('getTruncateTableSQL');

        $connection->expects($this->never())
            ->method('executeQuery');

        $dm->expects($this->once())
            ->method('getRepository')
            ->will($this->returnValue($repo));

        $dm->expects($this->once())
            ->method('getClassMetadata')
            ->will($this->returnValue($metadata));

        $dm->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue($connection));

        $documentCollection = $this->getMockBuilder('\MongoCollection')
            ->disableOriginalConstructor()
            ->getMock();

        $documentCollection
            ->expects($this->once())
            ->method('remove');

        $dm->expects($this->once())
            ->method('getDocumentCollection')
            ->will($this->returnValue($documentCollection));

        $self = $this;
        $dm->expects($this->any())
            ->method('persist')
            ->will($this->returnCallback(function ($argument) use ($self) {
                $self->assertNotNull($argument->getFirstAssociation());
                return true;
            }));

        return $dm;
    }

    public function testFluentInterface()
    {
        $writer = new DoctrineWriter($this->getEntityManager(), 'DdeboerDataImport:TestEntity');

        $association = new TestEntity();
        $item = array(
            'firstProperty'   => 'some value',
            'secondProperty'  => 'some other value',
            'firstAssociation'=> $association
        );

        $this->assertSame($writer, $writer->prepare());
        $this->assertSame($writer, $writer->writeItem($item));
        $this->assertSame($writer, $writer->finish());
    }
}
