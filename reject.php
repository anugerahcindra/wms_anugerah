<?php

require 'functions.php';

$id = $_GET['id'];

if(reject($id) > 0 ) {
		echo "<script>
				alert('Form ditolak!');
                document.location.href = 'form_persetujuan_in.php';
			  </script>"; 
	}

?>