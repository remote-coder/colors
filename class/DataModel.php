<?php

class DataModel
{
	
	/**
	 * Init a DB Connection for the app.  Assume single single db in the example app.
	 * @param array $config
	 * @throws Exception
	 * @return PDO
	 */
	public static function initDBConnection(array $config) {
		$initError = false;
		if(count($config)===0) {
			//empty config array? you got problems
			$initError = 1;
		} else {
			$requiredParms = array('host', 'port', 'dbName', 'user', 'password');
			foreach ($requiredParms as $key ) {
				if(!isset($config[$key])) {
					//missing config array element? you got problems
					$initError = 2;
					break;
				}
			}
		}

		if ($initError) {
			//Got a problem?  None shall Pass.
			throw new Exception('Invalid db init parms.');
		} else {
			//okay, give it a go.  Reach out and touch someone ('s database)
			$db = new PDO("mysql:host={$config['host']};port={$config['port']};dbname={$config['dbName']}", $config['user'], $config['password'], array( PDO::ATTR_PERSISTENT => false, PDO::ATTR_DEFAULT_FETCH_MODE=> PDO::FETCH_ASSOC));
			
			//Couldn't connect?  Awww man...thats no good.  Better log it.
			if ($db===false) {
				try{
					$errNo = mysql_errno();
					$errText = mysql_error();
					$errorText = "MySQL connect error: ERRNO:[{$errNo}]  ErrorText: [$errText]";
				} catch (Exception $e) {
					$errorText = print_r($e,1);
				}
				error_log($errorText);
				throw new Exception('Error Connecting to the Database');
			}			
		}
		
		//Otherwise, yay! We made a connection!
		return $db;
	}
	
	/**
	 * Just pour all the data into the return.  The mini-app has no pagination requirement.
	 * @param PDO $db
	 * @param array $params
	 * @return array
	 */
	public static function getColorData(PDO $db, $params=array()) {
		$values = $db
			->query("select * from color")
			->fetchAll();
		return $values;
	}
	
	/**
	 * Fantastic!  Someone wants the vote data for a specific color, give it to em!
	 * @param PDO $db
	 * @param array $params
	 * @return array
	 */
	public static function getVoteDataByColor(PDO $db, $params=array()) {
		$statement = $db
			->prepare("select * from votes a , color b " . 
					"where a.color_id = b.color_id and " .
					"b.color = :color");
		$statement->execute($params);
		$values = $statement->fetchAll();
		return $values;
	}
	
	
}