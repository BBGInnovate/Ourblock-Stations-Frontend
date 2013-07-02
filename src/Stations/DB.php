<?php
/**
* PDO wrapper class
* from: http://culttt.com/2012/10/01/roll-your-own-pdo-php-class/
*/
namespace Stations;

use PDO;

class Database
{
	private $host;
	private $user;
	private $pass;
	private $dbname;
	private $dbh;
	private $error;
	private $stmt;


	public function __construct()
	{
		//Set connection config
		$this->host 	= DB_HOST;
		$this->user 	= DB_USER;
		$this->pass 	= DB_PASS;
		$this->dbname 	= DB_NAME;

		//Create the Database Source Name string
		$dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname;
		$options = array(
			PDO::ATTR_PERSISTENT	=> true,
			PDO::ATTR_ERRMODE		=> PDO::ERRMODE_EXCEPTION
		);
		try{
			//Create a new PDO instance
			$this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
		} catch(PDOException $e){
				$this->error = $e->getMessage();
		}
	}

	/**
	* Prepare SQL query
	* @param string $query
	*/
	public function query($query)
	{
		$this->stmt = $this->dbh->prepare($query);
	}


	public function bind($param, $value, $type = null)
	{
		if (is_null($type)) {
			switch (true) {
				case is_int($value):
					$type = PDO::PARAM_INT;
					break;
				case is_bool($value):
					$type = PDO::PARAM_BOOL;
					break;
				case is_null($value):
					$type = PDO::PARAM_NULL;
					break;
				default:
					$type = PDO::PARAM_STR;
			}
		}
		$this->stmt->bindValue($param, $value, $type);
	}

	public function execute()
	{
		return $this->stmt->execute();
	}

	public function resultset()
	{
		$this->execute();
		return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public function single()
	{
		$this->execute();
		return $this->stmt->fetch(PDO::FETCH_ASSOC);
	}

	public function rowCount()
	{
		return $this->stmt->rowCount();
	}

	public function lastInsertId()
	{
		return $this->dbh->lastInsertId();
	}

	public function beginTransaction()
	{
		return $this->dbh->beginTransaction();
	}

	public function endTransaction()
	{
		return $this->dbh->commit();
	}

	public function cancelTransaction()
	{
		return $this->dbh->rollBack();
	}

	public function debugDumpParams(){
		return $this->stmt->debugDumpParams();
	}

	/**
	* Get a Category and it's associated Entity ID
	* @param $id int the category's id
	*/
	/*
	public function getCategoryById($id)
	{
		if(is_numeric($id)){
			$this->query("SELECT * FROM bbg_category WHERE bbg_category_id = :id");
			$this->bind(':id', $id);
			return $this->single();
		} else {
			return false;
		}
	}
	*/

	/**
	* Get a BBG Entity result
	* @param $id int
	*/
	/*
	public function getEntityById($id)
	{
		if(is_numeric($id)){
			$this->query("SELECT * FROM bbg_entity WHERE bbg_entity_id = :id");
			$this->bind(':id', $id);
			return $this->single();
		} else {
			return false;
		}
	}
	*/



}