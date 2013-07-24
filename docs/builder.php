<?php

$host="localhost"; 

$root="root"; 
$root_password=""; 

$user='parser';
$pass='parser';
$db="parsing"; 

try {
    $dbh = new PDO("mysql:host=$host", $root, $root_password);

    $dbh->exec("CREATE DATABASE `$db` DEFAULT CHARACTER SET UTF8;
        CREATE USER '$user'@'$host' IDENTIFIED BY '$pass';
        GRANT USAGE ON *.* TO '$user'@'$host' IDENTIFIED BY '$pass';
        GRANT ALL PRIVILEGES ON `$db`.* TO '$user'@'$host';"
    )or die(print_r($dbh->errorInfo(), true));

    $dbh = new PDO("mysql:host=$host; dbname=$db", $user, $pass);

    $sql = "CREATE TABLE articles (
		id INT NOT NULL AUTO_INCREMENT,
		title CHAR(200),
		description CHAR(255),
		img_path CHAR(250),
		cur_timestamp TIMESTAMP(8),
		PRIMARY KEY (id)
	) ENGINE = InnoDB;";

	if($dbh->exec($sql) !== false) {
		/**
		 * If the result of the execution is not false, the confirmation of new table created will be displayed.
		 */
		echo 'The table articles was created successfully';
	}
	
	/**
	 * Disconnect from the DB.
	 */
 	$dbh = null;
} catch (PDOException $e) {
    die("DB ERROR: ". $e->getMessage());
}