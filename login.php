<?php
session_start();
require_once "koneksi.php";

$pesan_login = "";
$pesan_register = "";
$tampilan = "login";

// ==========================
// PROSES LOGIN
// ==========================
if (isset($_POST['login'])) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = mysqli_prepare(
        $koneksi,
        "SELECT id, username, password, role
         FROM users
         WHERE username = ?"
    );

    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($user = mysqli_fetch_assoc($result)) {
        // Mendukung password hash dan password lama plaintext.
        $benar = password_verify($password, $user['password']);

        if (!$benar && $password === $user['password']) {
            $benar = true;
        }

        if ($benar) {
            session_regenerate_id(true);

            $_SESSION['login'] = true;
            $_SESSION['id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] === 'admin') {
                header("Location: dashboard_admin.php");
            } elseif ($user['role'] === 'siswa') {
                header("Location: dashboard_siswa.php");
            } elseif ($user['role'] === 'guru') {
                header("Location: daerah_guru.php");
            } else {
                header("Location: dashboard.php");
            }
            exit;
        }

        $pesan_login = "Password salah!";
    } else {
        $pesan_login = "Username tidak ditemukan!";
    }
}

// ==========================
// PROSES REGISTER
// ==========================
if (isset($_POST['register'])) {
    $tampilan = "register";

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';

    $nis = trim($_POST['nis'] ?? '');
    $nama_siswa = trim($_POST['nama_siswa'] ?? '');
    $kelas = trim($_POST['kelas'] ?? '');
    $jk_siswa = $_POST['jenis_kelamin_siswa'] ?? '';

    $nip = trim($_POST['nip'] ?? '');
    $nama_guru = trim($_POST['nama_guru'] ?? '');
    $jk_guru = $_POST['jenis_kelamin_guru'] ?? '';

    if ($username === '' || $password === '') {
        $pesan_register = "Username dan password wajib diisi!";
    } elseif (strlen($password) < 6) {
        $pesan_register = "Password minimal 6 karakter!";
    } elseif (!in_array($role, ['siswa', 'guru'], true)) {
        $pesan_register = "Silakan pilih role terlebih dahulu!";
    } elseif (
        $role === 'siswa' &&
        (
            $nis === '' ||
            $nama_siswa === '' ||
            $kelas === '' ||
            !in_array($jk_siswa, ['L', 'P'], true)
        )
    ) {
        $pesan_register = "Lengkapi semua data siswa!";
    } elseif (
        $role === 'guru' &&
        (
            $nip === '' ||
            $nama_guru === '' ||
            !in_array($jk_guru, ['L', 'P'], true)
        )
    ) {
        $pesan_register = "Lengkapi semua data guru!";
    } else {
        // Periksa username yang sudah digunakan.
        $stmt = mysqli_prepare(
            $koneksi,
            "SELECT id FROM users WHERE username = ?"
        );

        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $cek = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($cek) > 0) {
            $pesan_register = "Username sudah digunakan!";
        } else {
            mysqli_begin_transaction($koneksi);

            try {
                // Simpan akun.
                $hash = password_hash($password, PASSWORD_DEFAULT);

                $stmt = mysqli_prepare(
                    $koneksi,
                    "INSERT INTO users
                    (username, password, role, created_at)
                    VALUES (?, ?, ?, NOW())"
                );

                mysqli_stmt_bind_param(
                    $stmt,
                    "sss",
                    $username,
                    $hash,
                    $role
                );

                if (!mysqli_stmt_execute($stmt)) {
                    throw new Exception("Gagal menyimpan akun.");
                }

                $user_id = mysqli_insert_id($koneksi);

                // Simpan profil sesuai role.
                if ($role === 'siswa') {
                    $stmt = mysqli_prepare(
                        $koneksi,
                        "INSERT INTO siswa
                        (nis, nama_siswa, kelas, jenis_kelamin, user_id)
                        VALUES (?, ?, ?, ?, ?)"
                    );

                    mysqli_stmt_bind_param(
                        $stmt,
                        "ssssi",
                        $nis,
                        $nama_siswa,
                        $kelas,
                        $jk_siswa,
                        $user_id
                    );
                } else {
                    $stmt = mysqli_prepare(
                        $koneksi,
                        "INSERT INTO guru
                        (nip, nama, jenis_kelamin, user_id)
                        VALUES (?, ?, ?, ?)"
                    );

                    mysqli_stmt_bind_param(
                        $stmt,
                        "sssi",
                        $nip,
                        $nama_guru,
                        $jk_guru,
                        $user_id
                    );
                }

                if (!mysqli_stmt_execute($stmt)) {
                    throw new Exception("Gagal menyimpan profil.");
                }

                mysqli_commit($koneksi);

                $pesan_register =
                    "Register berhasil! Silakan login.";

                // Kosongkan form setelah berhasil.
                $_POST = [];

            } catch (Throwable $e) {
                mysqli_rollback($koneksi);

                $pesan_register =
                    "Register gagal. Periksa struktur kolom dan tipe data tabel.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login dan Register Siakad</title>

<style>
* {
    box-sizing: border-box;
    font-family: Arial, sans-serif;
}

body {
    margin: 0;
    min-height: 100vh;
    padding: 20px;
    display: flex;
    justify-content: center;
    align-items: center;
    background: #eef2f7;
}

.container {
    width: 100%;
    max-width: 430px;
    padding: 28px;
    background: white;
    border-radius: 14px;
    box-shadow: 0 8px 30px rgba(0,0,0,.12);
}

h2 {
    text-align: center;
    color: #243b64;
}

label {
    display: block;
    margin-top: 13px;
    font-size: 14px;
    font-weight: bold;
}

input, select {
    width: 100%;
    padding: 12px;
    margin-top: 6px;
    border: 1px solid #ccd3df;
    border-radius: 7px;
    background: white;
}

button {
    width: 100%;
    padding: 12px;
    margin-top: 18px;
    border: none;
    border-radius: 7px;
    background: #2459a9;
    color: white;
    font-weight: bold;
    cursor: pointer;
}

button:hover {
    background: #183f7b;
}

.pesan {
    padding: 10px;
    margin-bottom: 12px;
    border-radius: 6px;
    background: #fff0f0;
    color: #b42318;
    font-size: 14px;
}

.berhasil {
    background: #e8f8ed;
    color: #176b35;
}

.switch {
    text-align: center;
    margin-top: 18px;
    font-size: 14px;
}

.switch a {
    color: #2459a9;
    font-weight: bold;
    cursor: pointer;
}

.field-role {
    display: none;
}
</style>
</head>

<body>
<div class="container">

    <!-- LOGIN -->
    <div id="loginForm"
         style="<?= $tampilan === 'register' ? 'display:none' : '' ?>">

        <h2>Login Siakad</h2>

        <?php if ($pesan_login !== ''): ?>
            <div class="pesan">
                <?= htmlspecialchars($pesan_login) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <label>Username</label>
            <input type="text" name="username"
                   autocomplete="username" required>

            <label>Password</label>
            <input type="password" name="password"
                   autocomplete="current-password" required>

            <button type="submit" name="login">Login</button>
        </form>

        <div class="switch">
            Belum punya akun?
            <a onclick="showRegister()">Register</a>
        </div>
    </div>

    <!-- REGISTER -->
    <div id="registerForm"
         style="<?= $tampilan === 'register' ? 'display:block' : '' ?>">

        <h2>Register Siakad</h2>

        <?php if ($pesan_register !== ''): ?>
            <div class="pesan <?= strpos($pesan_register, 'berhasil') !== false ? 'berhasil' : '' ?>">
                <?= htmlspecialchars($pesan_register) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <label>Username</label>
            <input type="text" name="username"
                   value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                   autocomplete="username" required>

            <label>Password</label>
            <input type="password" name="password"
                   autocomplete="new-password"
                   minlength="6" required>

            <label>Pilih Role</label>
            <select name="role" id="role"
                    required onchange="tampilForm()">
                <option value="">-- Pilih Role --</option>
                <option value="siswa"
                    <?= ($_POST['role'] ?? '') === 'siswa' ? 'selected' : '' ?>>
                    Siswa
                </option>
                <option value="guru"
                    <?= ($_POST['role'] ?? '') === 'guru' ? 'selected' : '' ?>>
                    Guru
                </option>
            </select>

            <!-- FORM SISWA -->
            <div id="formSiswa" class="field-role">
                <label>NIS</label>
                <input type="text" name="nis" id="nis"
                       value="<?= htmlspecialchars($_POST['nis'] ?? '') ?>">

                <label>Nama Siswa</label>
                <input type="text" name="nama_siswa" id="nama_siswa"
                       value="<?= htmlspecialchars($_POST['nama_siswa'] ?? '') ?>">

                <label>Kelas</label>
                <input type="text" name="kelas" id="kelas"
                       value="<?= htmlspecialchars($_POST['kelas'] ?? '') ?>">

                <label>Jenis Kelamin</label>
                <select name="jenis_kelamin_siswa" id="jkSiswa">
                    <option value="">-- Pilih Jenis Kelamin --</option>
                    <option value="L"
                        <?= ($_POST['jenis_kelamin_siswa'] ?? '') === 'L' ? 'selected' : '' ?>>
                        Laki-laki
                    </option>
                    <option value="P"
                        <?= ($_POST['jenis_kelamin_siswa'] ?? '') === 'P' ? 'selected' : '' ?>>
                        Perempuan
                    </option>
                </select>
            </div>

            <!-- FORM GURU -->
            <div id="formGuru" class="field-role">
                <label>NIP</label>
                <input type="text" name="nip" id="nip"
                       value="<?= htmlspecialchars($_POST['nip'] ?? '') ?>">

                <label>Nama Guru</label>
                <input type="text" name="nama_guru" id="nama_guru"
                       value="<?= htmlspecialchars($_POST['nama_guru'] ?? '') ?>">

                <label>Jenis Kelamin</label>
                <select name="jenis_kelamin_guru" id="jkGuru">
                    <option value="">-- Pilih Jenis Kelamin --</option>
                    <option value="L"
                        <?= ($_POST['jenis_kelamin_guru'] ?? '') === 'L' ? 'selected' : '' ?>>
                        Laki-laki
                    </option>
                    <option value="P"
                        <?= ($_POST['jenis_kelamin_guru'] ?? '') === 'P' ? 'selected' : '' ?>>
                        Perempuan
                    </option>
                </select>
            </div>

            <button type="submit" name="register">Register</button>
        </form>

        <div class="switch">
            Sudah punya akun?
            <a onclick="showLogin()">Login</a>
        </div>
    </div>

</div>

<script>
function showRegister() {
    document.getElementById('loginForm').style.display = 'none';
    document.getElementById('registerForm').style.display = 'block';
}

function showLogin() {
    document.getElementById('registerForm').style.display = 'none';
    document.getElementById('loginForm').style.display = 'block';
}

function tampilForm() {
    const role = document.getElementById('role').value;
    const siswa = role === 'siswa';
    const guru = role === 'guru';

    document.getElementById('formSiswa').style.display =
        siswa ? 'block' : 'none';

    document.getElementById('formGuru').style.display =
        guru ? 'block' : 'none';

    document.getElementById('nis').required = siswa;
    document.getElementById('nama_siswa').required = siswa;
    document.getElementById('kelas').required = siswa;
    document.getElementById('jkSiswa').required = siswa;

    document.getElementById('nip').required = guru;
    document.getElementById('nama_guru').required = guru;
    document.getElementById('jkGuru').required = guru;
}

tampilForm();
</script>

</body>
</html>