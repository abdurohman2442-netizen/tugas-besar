<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Form Tambah Siswa</title>
    <link rel="stylesheet" href="style1.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body>

<?php require 'navbar.php'; ?>

<div class="form2" style="margin: 0 auto; width: 30%;">
    <div class="container text-center" style="margin: 0 auto;">
        <div class="form">
            <div class="card" style="margin-top: 20px;">
                
                <div class="card-form">
                    <h2>Form Tambah Siswa</h2><br>
                </div>

                <div class="card-body">
                    <form action="aksi_tambah_user.php" method="POST">
                        <h1>Data Profil Siswa</h1><br>

                        <div class="mb-3">
                            <input type="number" name="nis" class="form-control" placeholder="Nomor Induk Siswa | Contoh:12345678" style="width: 90%; margin: 0 auto;" required />
                        </div>
                        <br>

                        <div class="mb-3">
                            <input type="text" name="nama_siswa" class="form-control" placeholder="Nama Lengkap Siswa | Contoh:Ujang Asep" style="width: 90%; margin: 0 auto;" required />
                        </div>
                        <br>

                        <div class="mb-3">
                            <input type="text" name="kelas" class="form-control" placeholder="Kelas Siswa | Contoh:X-RPL-1" style="width: 90%; margin: 0 auto;" required />
                        </div>
                        <br>

                        <div class="radio">
                            Jenis Kelamin:<br>
                            <input type="radio" name="jenis_kelamin" id="L" value="L" class="form-check-input" required />
                            <label for="L">L</label><br>
                            <input type="radio" name="jenis_kelamin" id="P" value="P" class="form-check-input" required />
                            <label for="P">P</label>
                        </div>
                        <br>

                        <div class="form2" style="border: 1px solid #d3d3d3; padding: 10px;">
                            <h3>Data Kredensial Akun (Tabel Users)</h3><br>
                            
                            <div class="mb-3">
                                <input type="text" name="username" class="form-control" placeholder="Username Akun | Contoh:ujang123" style="width: 90%; margin: 0 auto;" required />
                            </div>
                            <br>

                            <div class="mb-3">
                                <input type="password" name="password" class="form-control" placeholder="Password Akun | Contoh:password123" style="width: 90%; margin: 0 auto;" required /> <br>
                                <input type="hidden" name="role" value="siswa">
                            </div>
                            <br>

                            <div class="button1">
                                <button type="submit" name="kirim" class="btn btn-primary" style="margin-bottom: 20px;">Simpan Data</button>
                                <a href="index.php" class="btn btn-danger" style="margin-bottom: 20px; display: inline-block; text-decoration: none;">Kembali</a>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>

<br><br>

<div class="form2" style="margin: 0 auto; width: 30%;">
    <div class="container text-center">
        <div class="card" style="margin-top: 20px;">
            <div class="card-form">
                <h2>Output dari Form</h2><br>
                <div class="card-get">
                <?php
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $nis           = $_POST['nis'] ?? '-';
                    $nama_siswa         = $_POST['nama_siswa'] ?? '-';
                    $kelas         = $_POST['kelas'] ?? '-';
                    $jenis_kelamin = $_POST['jenis_kelamin'] ?? '-';
                    $username      = $_POST['username'] ?? '-';
                    $password      = $_POST['password'] ?? '-';

                    echo "NIS: " . htmlspecialchars($nis) . "<br>";
                    echo "Nama: " . htmlspecialchars($nama_siswa) . "<br>";
                    echo "Kelas: " . htmlspecialchars($kelas) . "<br>";
                    echo "Jenis Kelamin: " . htmlspecialchars($jenis_kelamin) . "<br>";
                    echo "Username: " . htmlspecialchars($username) . "<br>";
                    echo "Password: " . htmlspecialchars($password) . "<br>";
                } else {
                    echo "<em>Belum ada data yang dikirim. Isi form di atas.</em>";
                }
                ?>
                </div>
            </div>
        </div>
    </div>
</div>
!-- SCRIPT INI WAJIB ADA AGAR DROPDOWN DAN JAVASCRIPT BOOTSTRAP BERJALAN -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<