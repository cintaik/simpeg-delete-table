<?php 
	
	mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

	try {
	    $connSchema = new mysqli("localhost:3306", "root", "", "INFORMATION_SCHEMA");
	    $connDB = new mysqli("localhost", "root", "", $dbTarget);
	} catch (mysqli_sql_exception $e) {
	    echo "There is something wrong #1 : " . $e->getMessage();
	}

