<?php

require 'functions.php';

$id = $_GET['id'];

if(rejectOUT($id) > 0 ) {
		echo "<script>
				alert('Form ditolak!');
                document.location.href = 'form_persetujuan_out.php';
			  </script>"; 
	}

?>