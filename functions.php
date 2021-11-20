<?php

$servername = 'localhost';
$username = 'root';
$password = '';
$database = 'wms';

$conn = mysqli_connect($servername, $username, $password, $database);

$result = mysqli_query($conn, "SELECT * FROM m_barang");


function query($query) 
{
	global $conn;

	$result = mysqli_query($conn, $query);
	$rows = [];
	while ($baris = mysqli_fetch_assoc($result) ) {
		$rows [] = $baris;
	}

	return $rows;
}



function tambah($data)
{
	global $conn;

	$kode_barang = htmlspecialchars($data["kode_brg"]);
	$nama_barang = htmlspecialchars($data["nama_brg"]);
	$uom = htmlspecialchars($data["uom"]);

	$foto = upload();
	if( !$foto){
		return false;
	}

	$query = "INSERT INTO m_barang  VALUES ('', '$kode_barang', '$nama_barang', '$uom', 0, '$foto')";

	mysqli_query($conn,$query);

	return mysqli_affected_rows($conn);
}


function transaksiIN($data)
{
	global $conn;

	$barang_in = htmlspecialchars($data["barang_in"]);
	$kode_barang = htmlspecialchars($data["kode_brg"]);
    $qty = htmlspecialchars($data["qty"]);

    $result = mysqli_query($conn, "SELECT * FROM `tr_barang_in` WHERE `kode_transaksi_in` = '$barang_in'");

	if ( mysqli_fetch_assoc($result) ) {
		echo "<script>
				alert('kode transaksi sudah ada!');
			  </script>";
		return false;
	}

	//cek apakah konfirmasi password sama atau tidak
	if( $password !== $password2) {
		echo "<script>
				alert('confirm password not matched');
			  </script>";
		return false;
	}

	$query = "INSERT INTO tr_barang_in  VALUES ('', '$barang_in', '$kode_barang', $qty, 'Draft')";

	mysqli_query($conn,$query);

	return mysqli_affected_rows($conn);
}


function barang_out($data)
{
	global $conn;

	$barang_out = htmlspecialchars($data["barang_out"]);
	$kode_barang = htmlspecialchars($data["kode_brg"]);
    $qty = htmlspecialchars($data["qty"]);

    //cek kode transaksi apakah sudah ada atau belum
    $result = mysqli_query($conn, "SELECT * FROM `tr_barang_out` WHERE `barang_out` = '$barang_out'");

	if ( mysqli_fetch_assoc($result) ) {
		echo "<script>
				alert('transaksi berhasil');
			  </script>";
		return false;
	}

    $dataout = mysqli_query($conn, "SELECT `qty` FROM `m_barang` WHERE `kode_brg` = '$kode_barang'");

    //untuk conversi dari object ke array
    $getData = mysqli_fetch_array($dataout);
    //get data qty berdasarkan inputan kode barang
    $stockSekarang = $getData['qty'];

    //cek apakah stok barang kurang dari 1
    if($stockSekarang <= 0){
        echo "<script>
                alert('stok barang saat ini kosong!');
              </script>";
        return false;
    }

    //cek apakah stok barang cukup
    if($stockSekarang < $qty ){
        echo "<script>
                alert('transaksi tidak bisa dilanjutkan stock barang kurang!');
              </script>";
        return false;
    }

	$query = "INSERT INTO tr_barang_out  VALUES ('', '$barang_out', '$kode_barang', $qty, 'Draft')";

	mysqli_query($conn,$query);

	return mysqli_affected_rows($conn);

    $query = "INSERT INTO tr_barang_out VALUES('', '$barang_out', '$kode_barang', '$qty', 'Draft')";

    mysqli_query($conn, $query);
    return mysqli_affected_rows($conn);

}



function delete($id)
{
	global $conn;

	mysqli_query($conn, "DELETE FROM tb_product WHERE id =$id");

	return mysqli_affected_rows($conn);

}



function approve($id)
{
	global $conn;

	$dataBarang = mysqli_query($conn, "SELECT tr_barang_in.qty, m_barang.kode_brg, m_barang.qty as qty2 FROM tr_barang_in INNER JOIN m_barang ON m_barang.kode_brg = tr_barang_in.kode_brg WHERE tr_barang_in.id = $id");

	$ambilData = mysqli_fetch_array($dataBarang);

	$stockSekarang = $ambilData['qty2'];
	$jumlah = $ambilData['qty'];
	$kode_brg = $ambilData['kode_brg'];

	$total = $stockSekarang + $jumlah;

    //untuk merubah jumlah atau qty
	$updateBrg = "UPDATE m_barang SET
					qty = $total
				WHERE kode_brg = '$kode_brg'";
	mysqli_query($conn, $updateBrg);	

    //untuk merubah status
	$query = "UPDATE tr_barang_in SET
				status = 'Approve'
			WHERE id = $id";
	mysqli_query($conn, $query);

	return mysqli_affected_rows($conn);
}

function approveOUT($id)
{
	global $conn;

	$dataBarang = mysqli_query($conn, "SELECT tr_barang_out.qty, m_barang.kode_brg, m_barang.qty as qty2 FROM tr_barang_out INNER JOIN m_barang ON m_barang.kode_brg = tr_barang_out.kode_brg WHERE tr_barang_out.id = $id");

	$ambilData = mysqli_fetch_array($dataBarang);

	$stockSekarang = $ambilData['qty2'];
	$jumlah = $ambilData['qty'];
	$kode_brg = $ambilData['kode_brg'];

	$total = $stockSekarang - $jumlah;

    //untuk merubah jumlah atau qty
	$updateBrg = "UPDATE m_barang SET
					qty = $total
				WHERE kode_brg = '$kode_brg'";
	mysqli_query($conn, $updateBrg);	

    //untuk merubah status
	$query = "UPDATE tr_barang_out SET
				status = 'Approve'
			WHERE id = $id";
	mysqli_query($conn, $query);

	return mysqli_affected_rows($conn);
}


function reject($id)
{
	global $conn;

	$query = "UPDATE tr_barang_in SET 
				status = 'reject' 
			  WHERE id = $id";

	mysqli_query($conn,$query);

	return mysqli_affected_rows($conn);

}


function rejectOUT($id)
{
	global $conn;

	$query = "UPDATE tr_barang_out SET 
				status = 'reject' 
			  WHERE id = $id";

	mysqli_query($conn,$query);

	return mysqli_affected_rows($conn);

}



function upload()
{
	$namaFoto = $_FILES['foto']['name'];
	$ukuranFoto = $_FILES['foto']['size'];
	$error = $_FILES['foto']['error'];
	$tmp_Name = $_FILES['foto']['tmp_name'];

	//cek jika tidak ada foto yg di upload
	if( $error === 4 ) {
		echo "<script>
				alert('silahkan upload file foto terlebih dahulu!');
		     </script>";

		return false;
	}

	//cek apakah yg di upload foto atau bukan
	$ekstensiFotoValid = ['jpg', 'png', 'jpeg'];
	$ekstensiFoto = explode('.', $namaFoto);
	$ekstensiFoto = strtolower(end($ekstensiFoto));

	if( !in_array($ekstensiFoto, $ekstensiFotoValid) ) {
		echo "<script>
				alert('file yang anda upload bukan foto atau gambar!');
		     </script>";

		return false;
	}

	//cek ukuran file foto
	if( $ukuranFoto > 1000000) {
		echo "<script>
				alert('ukuran file terlalu besar');
		     </script>";

		return false;
	}

	//generate nama file baru
	$namaFotoBaru = uniqid();
	$namaFotoBaru .= '.';
	$namaFotoBaru .= $ekstensiFoto;

	move_uploaded_file($tmp_Name, 'img/' . $namaFotoBaru);

	return $namaFotoBaru;
}



function register($data)
{
	global $conn;

	$username = htmlspecialchars($data["username"]);
	$password = mysqli_real_escape_string($conn, $data["password"]);
	$password2 = mysqli_real_escape_string($conn, $data["password2"]);
	$level = htmlspecialchars($data["level"]);


	//cek user apakah sudah ada atau belum
	$result = mysqli_query($conn, "SELECT `username` FROM `tb_user` WHERE `username` = '$username'");

	if ( mysqli_fetch_assoc($result) ) {
		echo "<script>
				alert('Maaf username sudah terdaftar!');
			  </script>";
		return false;
	}

	//cek apakah konfirmasi password sama atau tidak
	if( $password !== $password2) {
		echo "<script>
				alert('confirm password not matched');
			  </script>";
		return false;
	}

	
	$foto = upload();
	if( !$foto){
		return false;
	}

	//enkripsi password
	$password = password_hash($password, PASSWORD_DEFAULT);

	mysqli_query($conn,"INSERT INTO tb_user VALUES ('', '$username', '$password', '$level', '$foto')");

	return mysqli_affected_rows($conn);
}



function search($pencarian)
{
	$query = "SELECT * FROM tb_product WHERE
					kode_product LIKE '%$pencarian%' OR
					nama_product LIKE '%$pencarian%' OR
					nama_kategori LIKE '%$pencarian%'
					";

	return query($query);
}

?>