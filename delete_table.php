<?php 
	
	if (!isset($_GET["set"]) &&  $_GET["set"] != 1) {
		echo "	<script>
					alert('Proses tidak diizinkan !');
				</script>";
		exit();
	}else{

		// Khusus Memproses / Memeriksa Table yang Digunakan atau Tidak Digunakan

		$startTime = microtime(true);

		ini_set('max_execution_time', '0'); //300 seconds = 5 minutes
		ini_set('memory_limit','8589934592'); // 8GB RAN

		require_once "libs/koneksi_table.php";
		require_once "libs/fungsi_buatan_table.php";

		// Edit variable berikut
		$midDir = "BKD";
		$appName = "simpeg_2";
		$parentDIr = "C:\\xampp\\htdocs\\dashboard\\{$midDir}\\{$appName}";
		$db_target = "db_simpeg_medan_2";
		// END
		

		try {

		 	$sql = "SELECT TABLE_NAME FROM TABLES WHERE TABLE_SCHEMA = '".$db_target."'";

			$result = $connSchema->query($sql); // AND TABLE_NAME LIKE '%pelaksana%'
			if (!$result) {
		        throw new mysqli_sql_exception($connSchema->error);
		    }


			$used_tables = [];
			$unused_tables = [];

			$tableDigunakan = 0;
			// get all sources
			$sub_dir_list = getDirContents2($parentDIr);

			// Fetch all the tables 1 by 1

			if (!$row = $result->fetch_assoc()) {
		        throw new mysqli_sql_exception($connSchema->error);
		    }else{
		    	while ($row = $result->fetch_assoc()) {

					$tableDigunakan = 0;
					
					foreach ($sub_dir_list as $theFile) {

						// proses hanya dilakukan pada file.php saja
						$pecah = explode(".", $theFile);
						$cekPHP = end($pecah);
						if (strpos($cekPHP, "php") !== FALSE) {
							$file = $theFile; // full nama direktori + nama file php

							// check apakah nama table terdapat di $file
							if (cari_table($file, $row["TABLE_NAME"])) {
								$tableDigunakan++;
								

							} // END of check apakah nama table terdapat di $file
						} // END of proses hanya dilakukan pada file .php saja

					} // END of 2nd foreach

					if ($tableDigunakan > 0) {
						$used_tables[] = $row["TABLE_NAME"];
					}else{
						$unused_tables[] = $row["TABLE_NAME"];
					}
				} // END of 1st while loop
		    }

		} catch (mysqli_sql_exception $e) {
		    echo "There is something wrong #2 : " . $e->getMessage();
		}finally{
	      	$result->free();
	      	$connSchema->close();
		}

		// hapus table dari database
		deleteTable($connSchema, $connDB, $unused_tables, $db_target);

		echo "<br><br>";
		$endTime = microtime(true);
		$waktuEksekusi = $endTime - $startTime;
		echo "<h2>Waktu Proses : {$waktuEksekusi}</h2>";


		exit();
	}