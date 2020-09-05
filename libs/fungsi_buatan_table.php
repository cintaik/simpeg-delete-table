<?php 	
	
	const APP_NAME = "simpeg_2";
	// Khusus Memproses / Memeriksa Table yang Digunakan atau Tidak Digunakan
	
	/* 
		Kegunaan 	: Untuk mengetahui apakah $tableName terdapat di dalam $file atau tidak.
		Kekurangan 	: $tableName yang dikomentari juga dihitung
	*/
	function cari_table($file, $tableName){
		$fileku = fopen($file, "r");
		// $fileMod = file($file); 
		// buat diantara spasi
		// $tableName = " ".$tableName." "; // ditutup karena kemungkinan ada $sql ="table_name "
		if ($fileku) {
			while (!feof($fileku)) {
				$buffer = fgets($fileku);
				if (strpos($buffer, $tableName) !== FALSE) {
					fclose($fileku);
					return true;
				}
			}
			fclose($fileku);
			return false; //tidak ditemukan
		}else{
			// return "Tidak dapat membuka file <b>{$file}</b>! #2";
			return false;
		}
	}

	// tampilkan seluruh daftar path dari sub-dir suatu dir project
	function getDirContents2($parentDIr, $appName = APP_NAME){
		$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($parentDIr));
		$files = array();
		foreach ($rii as $file) {
		    if ($file->isDir()){ 
		        continue;
		    }
		    
		    $pathName = $file->getPathname();

		    $pecah = explode(".", $pathName);
		    $pecah_level = explode($appName, $pathName);
		    $get_level = count(array_filter(explode("\\",end($pecah_level)))); // agar simpeg/...php diperoleh

		     // setelah logika && pertama adalah path yang akan diabaikan
		     // sedangkan sebelum logika && pertama adalah path yang akan digunakan / scan
		    if ((strpos($pathName, "\\{$appName}\\isi\\") !== FALSE || 
		    	strpos($pathName, "\\{$appName}\\php\\") !== FALSE || 
		    	strpos($pathName, "\\{$appName}\\cetak\\") !== FALSE || 
		    	strpos($pathName, "\\{$appName}\\ajax\\") !== FALSE ||
		    	strpos($pathName, "\\php\\model\\") !== FALSE ||
		    	strpos($pathName, "\\php\\verifikasi_data_pegawai\\") !== FALSE ||
		    	$get_level == 1) &&
		    	(strpos($pathName, "\\{$appName}\\cetak\\ckeditor\\") === FALSE &&
		    	strpos($pathName, "\\ipdn\\FPDF\\") === FALSE &&
		    	strpos($pathName, "\\{$appName}\\ckfinder\\") === FALSE &&
		    	strpos($pathName, "\\css\\") === FALSE &&
		    	strpos($pathName, "\\js\\") === FALSE &&
		    	strpos($pathName, "\\php\\dompdf-0.6.2\\") === FALSE &&
		    	strpos($pathName, "\\php\\lib\\") === FALSE)){
		    	if (end($pecah) == "php") {
			    	$files[] = $pathName; 
			    }
		    }
		}
		return $files;
	}

	// Untuk menghapus table dari suatu database
	function deleteTable($connSchema, $connDB, $array_tbl, $dbTarget){
		$jlh_target_table = count($array_tbl);
		$tbl_not_found = "<br><br>Daftar Table Tidak Ditemukan :";
		$no = 0;
		foreach ($array_tbl as $tbl_to_delete) {
			$sqlSchema = "SELECT TABLE_NAME AS ada_table FROM TABLES WHERE TABLE_SCHEMA = '{$dbTarget}' AND TABLE_NAME = '{$tbl_to_delete}'";
			
			$sqlDb = "DROP TABLE {$tbl_to_delete}"; // delete field_name tanpa spasi
			
			try {

				$result = $connSchema->query($sqlSchema);
				$row = $result->fetch_assoc();
				if (!is_null($row["ada_table"])) {

					$connDB->autocommit(0);
					$connDB->query("SET FOREIGN_KEY_CHECKS=0"); // matikan cek constrain

					// cek apakah nama_table memiliki spasi
					if (preg_match("/\s/", $row["ada_table"])) {
						$newname = str_replace(" ", "_", $tbl_to_delete);
						$sqlRenameTable = "RENAME TABLE `{$tbl_to_delete}` TO `{$newname}`"; // rename table with whitespace
						$sqlDb = "DROP TABLE {$newname}"; // delete field_name dengan spasi
						$connDB->query($sqlRenameTable);
						$connDB->query($sqlDb);

					}else{
						$connDB->query($sqlDb);
					}

					$connDB->query("SET FOREIGN_KEY_CHECKS=1"); // hidupkan cek constrain
					$connDB->commit();

					// cek apakah berhasil dihapusdan tampilkan
					cekTable($connSchema, $dbTarget, $tbl_to_delete);

				}else{
					$no++;
					$tbl_not_found .= "{$no}) {$tbl_to_delete}<br>";
				}
			} catch (mysqli_sql_exception $e) {
				echo "<i>deleteTable()</i> " . $e->getMessage() . " " . $e->getLine() . "<br>";
			}
		}
		$result->free();

		$hasil = "Berhasil ! {$jlh_target_table}, Tabel tidak digunakan<br>";
		$hasil .= $tbl_not_found . "<br>";
		echo $hasil;
	}

	// Cek apakah ada redListTbl yang terlewatkan saat di deleteTable()
	function cekTable($connSchema, $dbTarget, $tbl_to_delete){

		$sqlSchema = "SELECT TABLE_NAME FROM TABLES WHERE TABLE_SCHEMA = '{$dbTarget}' AND TABLE_NAME = '{$tbl_to_delete}'";
		try {

			$result = $connSchema->query($sqlSchema);
			if (!$result->num_rows) {
				echo "<b>{$tbl_to_delete} has been deleted ! </b><br>";
			}
			
		} catch (mysqli_sql_exception $e) {
			echo "<i>cekTable()</i> " . $e->getMessage() . " " . $e->getLine() . "<br>";
		}finally{
			$result->free();
		}
	}


