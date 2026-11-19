<?php

$koneksi = mysqli_connect("localhost", "root", "", "db_siacad_smk");

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>