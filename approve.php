<?php

require 'functions.php';

$id = $_GET['id'];

if(approve($id) > 0 ) {
		echo "<script>
				alert('Form telah disetujui!');
                document.location.href = 'form_persetujuan_in.php';
			  </script>"; 
	}

?>