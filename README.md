PDO-Active-Record
=================

A PDO Active Record Class for PHP


Just instantiate the class and go!

```php
	//Set Database Configuration
	
	$config = array(
		'host' => 'XXX'
		,'db_name' => 'XXX'
		,'db_username' => 'XXX'
		,'db_password' => 'XXX'
	);
	
	//Instantiate DB Connection
	
	$RemoteDB = new PDOActiveRecord($config);
	
	//Make Calls
	
	$FieldValue = $RemoteDB->getByField($tablename, $fieldname, $value);
	
	//or
	
	$Objects = $RemoteDB->getAllByWhere($tablename, $array);
	
	//or 
	
	$NewObject = $RemoteDB->create($tablename, $array);
```


If you don't want to pass a $config variable everytime, create a new class that extends PDOActiveRecord with the config variables set and instantiate that class, instead:
```php
	<?php 
	class MyRemoteDB extends PDOActiveRecord
	{
	  	public $host = 'XXX';
		public $db_name = 'XXX';
		public $db_username = 'XXX';
		public $db_password = 'XXX';
	}
	?>
	
	$RemoteDB = new MyRemoteDB();
```
