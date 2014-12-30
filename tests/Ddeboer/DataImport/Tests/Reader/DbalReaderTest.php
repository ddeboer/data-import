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
        $this->assertEquals(array('id', 'username', 'name'), $fields);
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
            $this->assertEquals('user-'.$i, $row['username']);
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
            'driver' => 'pdo_sqlite',
            'memory' => true,
        );

        $connection = DriverManager::getConnection($params, new Configuration());

        $schema = new \Doctrine\DBAL\Schema\Schema();

        $table = $schema->createTable('groups');
        $table->addColumn('id', 'integer');
        $table->addColumn('name', 'string', array('length' => 45));
        $table->setPrimaryKey(array('id'));

        $myTable = $schema->createTable('user');
        $myTable->addColumn('id', 'integer', array('unsigned' => true, 'autoincrement' => true));
        $myTable->addColumn('username', 'string', array('length' => 32));
        $myTable->addColumn('group_id', 'integer');
        $myTable->setPrimaryKey(array('id'));
        $myTable->addUniqueIndex(array('username'));
        $myTable->addForeignKeyConstraint($table, array('group_id'), array('id'));

        foreach ($schema->toSql(new SqlitePlatform) as $query) {
            $connection->query($query);
        };

        return $connection;
    }

    protected function getReader()
    {
        $connection = $this->getConnection();

        $counter = 1;
        for ($i = 1; $i <= 10; $i++) {
            $connection->insert('groups', array('name' => "name {$i}"));
            $id = $connection->lastInsertId();

            for ($j = 1; $j <= 10; $j++) {
                $connection->insert('user', array(
                    'username' => "user-{$counter}",
                    'group_id' => $id,
                ));

                $counter++;
            }
        }

        return new DbalReader($connection, 'SELECT u.id, u.username, g.name FROM `user` u INNER JOIN groups g ON u.group_id = g.id');
    }
}
