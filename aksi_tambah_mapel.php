<?php
require_once 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $kode_mapel = trim($_POST['kode_mapel'] ?? '');
    $nama_mapel = trim($_POST['nama_mapel'] ?? '');
    $nama_guru  = trim($_POST['nama_guru'] ?? '');

    if ($kode_mapel === '' || $nama_mapel === '' || $nama_guru === '') {
        die("Semua field wajib diisi.");
    }

    $sql  = "INSERT INTO mapel (kodemapel, namamapel, namaguru, created_at)
             VALUES (?, ?, ?, NOW())";
    $stmt = mysqli_prepare($koneksi, $sql);
    mysqli_stmt_bind_param($stmt, "sss", $kode_mapel, $nama_mapel, $nama_guru);

    if (!mysqli_stmt_execute($stmt)) {
        die("Gagal menyimpan data mapel: " . mysqli_stmt_error($stmt));
    }

    mysqli_stmt_close($stmt);

    echo "<script>
        alert('Data mapel berhasil disimpan!');
        window.location.href='data_mapel.php';
    </script>";
    exit;
}
?>