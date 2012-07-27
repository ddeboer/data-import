<?php

namespace Ddeboer\DataImport\Tests\Reader;

use Ddeboer\DataImport\Reader\DbalReader;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Platforms\SqlitePlatform;

class DbalReaderTest extends \PHPUnit_Framework_TestCase
{
    public function testGetFields()
    {
        $fields = $this->getReader()->getFields();
        $this->assertInternalType('array', $fields);
        $this->assertEquals(array('id', 'username'), $fields);
    }

    public function testCount()
    {
        $this->assertEquals(100, $this->getReader()->count());
    }

    public function testIterate()
    {
        $i=1;
        foreach ($this->getReader() as $row) {
            $this->assertInternalType('array', $row);
            $this->assertEquals('user' . $i, $row['username']);
            $i++;
        }
    }

    public function testReaderRewindWorksCorrectly()
    {
        $reader = $this->getReader();
        foreach ($reader as $row) {
        }

        foreach ($reader as $row) {
        }
    }

    public function getConnection()
    {
        $params = array(
            'driver' => 'pdo_sqlite'
        );
        $connection = DriverManager::getConnection($params, new Configuration());

        $schema = new \Doctrine\DBAL\Schema\Schema();
        $myTable = $schema->createTable('user');
        $myTable->addColumn('id', 'integer', array('unsigned' => true));
        $myTable->addColumn('username', 'string', array('length' => 32));
        $myTable->setPrimaryKey(array('id'));
        $myTable->addUniqueIndex(array('username'));

        foreach ($schema->toSql(new SqlitePlatform) as $query) {
            $connection->query($query);
        };

        return $connection;
    }

    protected function getReader()
    {
        $connection = $this->getConnection();
        for ($i = 1; $i <= 100; $i++) {
            $connection->insert('user', array('username' => 'user' . $i));
        }

        return new DbalReader($connection, 'user');
    }
}