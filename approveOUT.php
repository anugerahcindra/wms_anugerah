<?php

require 'functions.php';

$id = $_GET['id'];

if(approveOUT($id) > 0 ) {
		echo "<script>
				alert('Form telah disetujui!');
                document.location.href = 'form_persetujuan_out.php';
			  </script>"; 
	}

?>