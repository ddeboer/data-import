<?php

namespace Ddeboer\DataImport\Tests\Reader;

use Ddeboer\DataImport\Reader\PdoReader;
// use Doctrine\DBAL\Configuration;
// use Doctrine\DBAL\DriverManager;
// use Doctrine\DBAL\Platforms\SqlitePlatform;

class PdoReaderTest extends \PHPUnit_Framework_TestCase
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
		$connection = new \PDO('sqlite::memory:');
		// Set error mode = exception for easy debugging
		$connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

		// Build schema
		$connection->query('CREATE TABLE groups (
			id INTEGER PRIMARY KEY,
			name VARCHAR(45)
		)');

		$connection->query('CREATE TABLE user (
			id INTEGER PRIMARY KEY,
			username VARCHAR(32),
			group_id INTEGER,
			FOREIGN KEY(group_id) REFERENCES groups(id)
		)');
		$connection->query('CREATE UNIQUE INDEX user_username ON user(username)');

		return $connection;
	}

	protected function getReader()
	{
		$connection = $this->getConnection();

		$group_insert = $connection->prepare('INSERT INTO groups (name) VALUES (:name)');
		$user_insert  = $connection->prepare('INSERT INTO user (username, group_id) VALUES (:username, :group)');

		$counter = 1;
		for ($i = 1; $i <= 10; $i++) {
			$group_insert->execute([':name' => "name {$i}"]);
			$id = $connection->lastInsertId();

			for ($j = 1; $j <= 10; $j++) {
				$user_insert->execute([
					':username'  => "user-{$counter}",
					':group' => $id
				]);

				$counter++;
			}
		}

		return new PdoReader($connection, 'SELECT u.id, u.username, g.name FROM `user` u INNER JOIN groups g ON u.group_id = g.id');
	}
}
