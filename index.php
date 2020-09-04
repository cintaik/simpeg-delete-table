<script>
	window.onload = showConfirm("Anda yakin ingin menjalankan simpeg_delete ?");
	function showConfirm(message){
		var confVar = confirm(message);
		if (confVar) {
			window.location.href='http://localhost/dashboard/BKD/simpeg_delete_table/delete_table.php?set=1';
		}
	}
</script>