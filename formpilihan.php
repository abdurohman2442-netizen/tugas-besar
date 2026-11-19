
<?php
require_once "koneksi.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $role = $_POST["role"] ?? "";
    $username = trim($_POST["username"] ?? "");
    $password_input = $_POST["password"] ?? "";

    if (
        !in_array($role, ["admin", "guru", "siswa"], true) ||
        $username === "" ||
        $password_input === ""
    ) {
        echo "<script>
            alert('Role, username, dan password wajib diisi!');
            history.back();
        </script>";
        exit;
    }

    mysqli_begin_transaction($koneksi);

    try {
        // SIMPAN AKUN KE TABEL USERS
        $password = password_hash(
            $password_input,
            PASSWORD_DEFAULT
        );

        $stmt = mysqli_prepare(
            $koneksi,
            "INSERT INTO users (username, password, role)
             VALUES (?, ?, ?)"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "sss",
            $username,
            $password,
            $role
        );

        mysqli_stmt_execute($stmt);
        $user_id = mysqli_insert_id($koneksi);
        mysqli_stmt_close($stmt);

        // SIMPAN DATA SISWA
        if ($role === "siswa") {

            $nis = trim($_POST["nis"] ?? "");
            $nama_siswa = trim($_POST["nama_siswa"] ?? "");
            $kelas = trim($_POST["kelas"] ?? "");
            $jk = $_POST["jenis_kelamin_siswa"] ?? "";

            if (
                $nis === "" ||
                $nama_siswa === "" ||
                $kelas === "" ||
                !in_array($jk, ["L", "P"], true)
            ) {
                throw new Exception("Data siswa belum lengkap");
            }

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
                $jk,
                $user_id
            );

            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }

        // SIMPAN DATA GURU
        elseif ($role === "guru") {

            $nip = trim($_POST["nip"] ?? "");
            $nama = trim($_POST["nama"] ?? "");
            $jk = $_POST["jenis_kelamin_guru"] ?? "";

            if (
                $nip === "" ||
                $nama === "" ||
                !in_array($jk, ["L", "P"], true)
            ) {
                throw new Exception("Data guru belum lengkap");
            }

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
                $nama,
                $jk,
                $user_id
            );

            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }

        mysqli_commit($koneksi);

        echo "<script>
            alert('Data berhasil ditambahkan!');
            window.location.href = 'daftar_user.php';
        </script>";
        exit;

    } catch (Throwable $e) {

        mysqli_rollback($koneksi);
        error_log($e->getMessage());

        echo "<script>
            alert('Gagal menyimpan data. Pastikan NIS, NIP, dan username belum digunakan serta struktur tabel benar.');
            history.back();
        </script>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah User</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<?php require_once "navbar.php"; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">

                    <h3 class="fw-bold mb-1">Tambah User</h3>
                    <p class="text-muted mb-4">
                        Pilih role untuk mengisi data pengguna.
                    </p>

                    <form action="formpilihan.php" method="POST">

                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select name="role" id="role"
                                    class="form-select"
                                    onchange="ubahForm()" required>
                                <option value="">-- Pilih Role --</option>
                                <option value="admin">Admin</option>
                                <option value="siswa">Siswa</option>
                                <option value="guru">Guru</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username"
                                   class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password"
                                   class="form-control" required>
                        </div>

                        <!-- FORM SISWA -->
                        <div id="formSiswa" style="display:none;">
                            <hr>
                            <h5 class="fw-bold mb-3">Data Siswa</h5>

                            <div class="mb-3">
                                <label class="form-label">NIS</label>
                                <input type="text" name="nis"
                                       id="nis" class="form-control">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nama Siswa</label>
                                <input type="text" name="nama_siswa"
                                       id="namaSiswa" class="form-control">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Kelas</label>
                                <input type="text" name="kelas"
                                       id="kelas" class="form-control"
                                       placeholder="Contoh: XI RPL 1">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Jenis Kelamin</label>
                                <select name="jenis_kelamin_siswa"
                                        id="jkSiswa" class="form-select">
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                            </div>
                        </div>

                        <!-- FORM GURU -->
                        <div id="formGuru" style="display:none;">
                            <hr>
                            <h5 class="fw-bold mb-3">Data Guru</h5>

                            <div class="mb-3">
                                <label class="form-label">Nama Guru</label>
                                <input type="text" name="nama"
                                       id="namaGuru" class="form-control">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">NIP</label>
                                <input type="text" name="nip"
                                       id="nip" class="form-control">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Jenis Kelamin</label>
                                <select name="jenis_kelamin_guru"
                                        id="jkGuru" class="form-select">
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                            </div>
                        </div>

                        <!-- FORM ADMIN -->
                        <div id="formAdmin" style="display:none;">
                            <hr>
                            <div class="alert alert-info">
                                Admin tidak membutuhkan data tambahan.
                            </div>
                        </div>

                        <button type="submit"
                                class="btn btn-primary w-100 mt-3">
                            Simpan Data
                        </button>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
function ubahForm() {
    const role = document.getElementById("role").value;

    const formSiswa = document.getElementById("formSiswa");
    const formGuru = document.getElementById("formGuru");
    const formAdmin = document.getElementById("formAdmin");

    formSiswa.style.display = "none";
    formGuru.style.display = "none";
    formAdmin.style.display = "none";

    document.querySelectorAll(
        "#formSiswa input, #formSiswa select, " +
        "#formGuru input, #formGuru select"
    ).forEach(function(input) {
        input.required = false;
    });

    if (role === "siswa") {
        formSiswa.style.display = "block";

        ["nis", "namaSiswa", "kelas", "jkSiswa"].forEach(function(id) {
            document.getElementById(id).required = true;
        });

    } else if (role === "guru") {
        formGuru.style.display = "block";

        ["namaGuru", "nip", "jkGuru"].forEach(function(id) {
            document.getElementById(id).required = true;
        });

    } else if (role === "admin") {
        formAdmin.style.display = "block";
    }
}
</script>

</body>
</html>
