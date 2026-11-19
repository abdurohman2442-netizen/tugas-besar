<?php

require_once 'koneksi.php';

// Pastikan form dikirim menggunakan POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: daftar_mapel.php");
    exit;
}

// Ambil data dari form edit
$id = (int) ($_POST['id'] ?? 0);
$kode_mapel = trim($_POST['kodemapel'] ?? '');
$nama_mapel = trim($_POST['namamapel'] ?? '');
$nama_guru = trim($_POST['namaguru'] ?? '');

// Validasi data
if (
    $id <= 0 ||
    $kode_mapel === '' ||
    $nama_mapel === '' ||
    $nama_guru === ''
) {
    echo "<script>
        alert('Semua data mapel wajib diisi!');
        window.history.back();
    </script>";
    exit;
}

// Pastikan data mapel yang akan diedit tersedia
$cek = mysqli_prepare(
    $koneksi,
    "SELECT id FROM mapel WHERE id = ?"
);

mysqli_stmt_bind_param($cek, "i", $id);
mysqli_stmt_execute($cek);
mysqli_stmt_store_result($cek);

if (mysqli_stmt_num_rows($cek) === 0) {
    mysqli_stmt_close($cek);
    die("Data mapel tidak ditemukan.");
}

mysqli_stmt_close($cek);

// Update data mapel berdasarkan ID
$query = mysqli_prepare(
    $koneksi,
    "UPDATE mapel
     SET kodemapel = ?,
         namamapel = ?,
         namaguru = ?
     WHERE id = ?"
);

mysqli_stmt_bind_param(
    $query,
    "sssi",
    $kode_mapel,
    $nama_mapel,
    $nama_guru,
    $id
);

// Jalankan update
if (mysqli_stmt_execute($query)) {

    mysqli_stmt_close($query);

    echo "<script>
        alert('Data mapel berhasil diubah!');
        window.location.href = 'daftar_mapel.php';
    </script>";
    exit;

} else {

    $error = mysqli_stmt_error($query);
    mysqli_stmt_close($query);

    die("Gagal mengubah data mapel: " . htmlspecialchars($error));
}

?>