<?php
require_once 'koneksi.php';

// Ambil data guru dari database
$query_guru = mysqli_query(
    $koneksi,
    "SELECT nama FROM guru ORDER BY nama ASC"
);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Form Tambah Mapel</title>

    <link rel="stylesheet" href="style1.css">
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body>

<?php require 'navbar.php'; ?>

<div style="margin: 0 auto; width: 90%; max-width: 450px;">
    <div class="container text-center">
        <div class="card mt-4">

            <form action="aksi_tambah_mapel.php" method="POST">
                <div style="border: 1px solid #d3d3d3; padding: 20px;">

                    <h3>Tabel Mapel Guru</h3>

                    <div class="mb-3 mt-4">
                        <input type="text"
                               name="kode_mapel"
                               class="form-control"
                               placeholder="Masukkan Kode Mapel"
                               required>
                    </div>

                    <div class="mb-3">
                        <input type="text"
                               name="nama_mapel"
                               class="form-control"
                               placeholder="Masukkan Nama Mapel"
                               required>
                    </div>

                    <div class="mb-3">
                        <select name="nama_guru"
                                class="form-select"
                                required>

                            <option value="">-- Pilih Nama Guru --</option>

                            <?php while ($guru = mysqli_fetch_assoc($query_guru)) { ?>
                                <option value="<?= htmlspecialchars($guru['nama']) ?>">
                                    <?= htmlspecialchars($guru['nama']) ?>
                                </option>
                            <?php } ?>

                        </select>
                    </div>

                    <div class="button1">
                        <button type="submit"
                                name="kirim"
                                class="btn btn-primary mb-3">
                            Simpan Data
                        </button>

                        <a href="index.php"
                           class="btn btn-danger mb-3">
                            Kembali
                        </a>
                    </div>

                </div>
            </form>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>