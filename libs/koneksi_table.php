<?php 
	
	mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

	try {
	    $connSchema = new mysqli("localhost:3306", "root", "", "INFORMATION_SCHEMA");
	    $connDB = new mysqli("localhost", "root", "", $db_target);
	} catch (mysqli_sql_exception $e) {
	    echo "There is something wrong #1 : " . $e->getMessage();
	}

