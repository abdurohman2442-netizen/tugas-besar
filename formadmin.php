
<?php
include "koneksi.php";

if (isset($_POST['simpan'])) {

    $username = trim($_POST['username'] ?? '');
    $password_input = $_POST['password'] ?? '';

    if ($username === '' || $password_input === '') {
        echo "<script>
            alert('Username dan password wajib diisi!');
            history.back();
        </script>";
        exit;
    }

    $password = password_hash($password_input, PASSWORD_DEFAULT);

    $stmt = mysqli_prepare(
        $koneksi,
        "INSERT INTO users (username, password, role)
         VALUES (?, ?, 'admin')"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "ss",
        $username,
        $password
    );

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);

        echo "<script>
            alert('Admin berhasil ditambahkan!');
            window.location = 'user.php';
        </script>";
        exit;
    } else {
        error_log(mysqli_stmt_error($stmt));
        mysqli_stmt_close($stmt);

        echo "<script>
            alert('Gagal menyimpan admin. Periksa username atau database!');
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
    <title>Tambah Admin</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">
</head>

<body class="bg-light">

<?php require 'navbar.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <h3 class="text-center">Form Tambah Admin</h3>

            <div class="card mt-4 shadow-sm">
                <div class="card-body">

                    <h5 class="mb-4">Data Akun Admin</h5>

                    <form method="POST">

                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input
                                type="text"
                                name="username"
                                class="form-control"
                                placeholder="Masukkan username"
                                required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input
                                type="password"
                                name="password"
                                class="form-control"
                                placeholder="Masukkan password"
                                required>
                        </div>

                        
                            <a href="daftar_user.php" class="btn btn-secondary">
                            simpan
                        </a>
                        

                        <a href="daftar_user.php" class="btn btn-secondary">
                            Kembali
                        </a>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
