<?php

require_once 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $role     = $_POST['role'] ?? '';

    // Hash password
    $pass = password_hash($password, PASSWORD_DEFAULT);


    // =========================
    // SIMPAN KE TABEL USERS
    // =========================

    $sql_users = "INSERT INTO users
                  (username, password, role, created_at)
                  VALUES
                  ('$username', '$pass', '$role', NOW())";

    $query_users = mysqli_query($koneksi, $sql_users);

    if (!$query_users) {
        die("Gagal menyimpan users: " . mysqli_error($koneksi));
    }

    // Ambil ID user yang baru dibuat
    $user_id = mysqli_insert_id($koneksi);


    // =========================
    // ROLE SISWA
    // =========================

    if ($role == "siswa") {

        $nis           = $_POST['nis'] ?? '';
        $nama_siswa          = $_POST['nama_siswa'] ?? '';
        $kelas         = $_POST['kelas'] ?? '';
        $jenis_kelamin = $_POST['jenis_kelamin'] ?? '';

        $sql = "INSERT INTO siswa
                (nis, nama_siswa, kelas, jenis_kelamin, user_id)
                VALUES
                ('$nis', '$nama_siswa', '$kelas', '$jenis_kelamin', '$user_id')";

        if (!mysqli_query($koneksi, $sql)) {
            die("Gagal menyimpan data siswa: " . mysqli_error($koneksi));
        }
    }


    // =========================
    // ROLE GURU
    // =========================

    elseif ($role == "guru") {

        $nip            = $_POST['nip'] ?? '';
        $nama           = $_POST['nama'] ?? '';
        $jenis_kelamin  = $_POST['jenis_kelamin'] ?? '';

        $sql = "INSERT INTO guru
                (nip, nama, jenis_kelamin, user_id)
                VALUES
                ('$nip', '$nama', '$jenis_kelamin', '$user_id')";

        if (!mysqli_query($koneksi, $sql)) {
            die("Gagal menyimpan data guru: " . mysqli_error($koneksi));
        }
    }


    // =========================
    // ROLE ADMIN
    // =========================

    elseif ($role == "admin") {

        // Admin hanya disimpan di tabel users
    }


    // =========================
    // BERHASIL
    // =========================

    echo "<script>
        alert('Data $role berhasil disimpan!');
        window.location.href='data_user.php';
    </script>";

    exit;

} else {

    header("Location: form.php");
    exit;

}

?>