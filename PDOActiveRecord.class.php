<?php

class PDOActiveRecord
{
  public $host;
	public $db_name; 
	public $db_username; 
	public $db_password;
	public $dbh;
	
	function __construct($config = false) 
	{
		if(is_array($config))
		{
			$this->host = $config['host'];
			$this->db_name = $config['db_name'];
			$this->db_username = $config['db_username'];
			$this->db_password = $config['db_password'];
		}	
		if(empty($this->host) || empty($this->db_name))
		{
			throw new Exception('Configuration Host and Database Name no properly set');
		}
		try 
		{
		    $this->dbh = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->db_username, $this->db_password);
		    $this->dbh->exec("SET CHARACTER SET utf8");
		}
		catch (PDOException $e)
		{
		    throw new Exception($e->getMessage());
		}
	}
	
	public function create($table, $insert, $timestamp = true)
	//Timestamp always set unless otherwise specified
	{
		if(!isset($insert['created_at']) && $timestamp)
		{
			$insert['created_at'] = date("Y-m-d g:i:s");
		}
		
		// Filter out fields that don't exist
		$insert = $this->filterInsert($insert, $table);
		//End Filter
		
		
		$keys = implode(', ', array_keys($insert));
		$table_values = implode(", :", array_keys($insert));
		$sql = "INSERT INTO $table ($keys) VALUES(:$table_values)";
		$query = $this->dbh->prepare($sql);
		$new_insert = array();
		foreach($insert as $key=>$value)
		{
			if($value==null)
			{
				$value = '';
			}
			$new_insert[":" . $key] = $value;
		}
		$query->execute($new_insert);
		
		//to check that there is an id field before using it to get the last object
		if($this->dbh->lastInsertId())
		{
			$stmt = $this->dbh->query("SELECT * FROM $table WHERE id='" . $this->dbh->lastInsertId() . "'");
			return $stmt->fetch(PDO::FETCH_OBJ);
		}
		else
		//if there isn't, just get the object by fields
		{
			if($timestamp)
			{
				unset($insert['created_at']);
			}
			return $this->getByWhere($table, $insert);
		}
		

	}
	
	public function update($table, $insert, $object)
	{
		$tmp = array();
		$insert = $this->filterInsert($insert, $table);
		foreach($insert as $key=>$value)
		{
			$tmp[] = "$key=?";
		}
		$str = implode(', ', $tmp);
		$sql = "UPDATE $table SET $str WHERE id='" . $object->id . "'";
		$query = $this->dbh->prepare($sql);
		$query->execute(array_values($insert));
		return $this->dbh->exec($sql);

	}
	
	public function getByID($table, $id)
	{
		$stmt = $this->dbh->query("SELECT * FROM $table WHERE id='" . $id  . "'");	
		return $stmt->fetch(PDO::FETCH_OBJ);
	}
	
	public function getByField($table, $field, $value, $options = false)
	{
		$sql = "SELECT * FROM $table WHERE $field='" . $value  . "'";
		if($options)
		{
			$sql .= ' ' . $options;
		}
		$stmt = $this->dbh->query($sql);	
		return $stmt->fetch(PDO::FETCH_OBJ);
	}
	
	public function getByWhere($table, $data, $options = false)
	{
		$conditions = array();
		foreach($data as $key=>$value)
		{
			if($value==null)
			{
				$conditions[] = "$key IS NULL";
				unset($data[$key]);
			}
			else
			{
				$conditions[] = "$key=?";
			}
		}
		$str = implode(' AND ', $conditions);
		$sql = "SELECT * FROM $table WHERE $str";
		if($options)
		{
			$sql .= ' ' . $options;
		}
		$query = $this->dbh->prepare($sql);
		$query->execute(array_values($data));
		return $query->fetch(PDO::FETCH_OBJ);			
	}
	
	public function getAllByWhere($table, $data, $options = false)
	{
		$conditions = array();
		foreach($data as $key=>$value)
		{
			if($value==null)
			{
				$conditions[] = "$key IS NULL";
				unset($data[$key]);
			}
			else
			{
				$conditions[] = "$key=?";
			}
		}
		$str = implode(' AND ', $conditions);
		$sql = "SELECT * FROM $table WHERE $str";
		if($options)
		{
			$sql .= ' ' . $options;
		}
		$query = $this->dbh->prepare($sql);
		$query->execute(array_values($data));
		return $query->fetchAll(PDO::FETCH_OBJ);			
	}
	
	public function filterInsert($insert, $table)
	{
		$columns = $this->dbh->query("SHOW COLUMNS FROM `$table`")->fetchAll();
		$fields = array();
		foreach($columns as $row)
		{
			$fields[$row['Field']] = true;
		}
		
		foreach($insert as $key=>$value)
		{
			if(!isset($fields[$key]))
			{
				unset($insert[$key]);
			}
		}
		
		if(isset($insert['created_at']))
		{
			if(ctype_digit($insert['created_at']))
			{
				$insert['created_at'] = date("Y-m-d g:i:s", $insert['created_at']);
			}
		}
		
		if(isset($insert['updated_at']))
		{
			if(ctype_digit($insert['updated_at']))
			{
				$insert['updated_at'] = date("Y-m-d g:i:s", $insert['updated_at']);
			}
		}
		
		return $insert;
	}
}
