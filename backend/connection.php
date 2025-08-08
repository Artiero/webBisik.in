<?php
$conn = mysqli_connect('localhost','root','root','db_problem');

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
