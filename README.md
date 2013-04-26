PDO-Active-Record
=================

A PDO Active Record Class for PHP

Extend the PDOActiveRecord class with another class for each database connection:
```php
	<?php 
	class MyRemoteDB extends PDOActiveRecord
	{
	  	public $host = 'host';
		public $db_name = 'dbname';
		public $db_username = 'username';
		public $db_password = 'password';
	}
	?>
```

Then, just instantiate that class and go!
```php
	//Instantiate DB Connection
	
	$RemoteDB = new MyRemoteDB();
	
	//Make Calls
	
	$FieldValue = $RemoteDB->getByField($tablename, $fieldname, $value);
	
	or
	
	$Objects = $RemoteDB->getAllByWhere($tablename, $array);
	
	or 
	
	$NewObject = $RemoteDB->create($tablename, $array);
```
