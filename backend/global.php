<?php
session_start();
if (!isset($_SESSION['user_token'])) {
    $_SESSION['user_token'] = bin2hex(random_bytes(16)); // session unik
}

require 'connection.php';

function queryData($data)
{
    global $conn;
    $result = mysqli_query($conn, $data);

    // Cek apakah query berhasil
    if (!$result) {
        // Hanya tampilkan error saat development (bisa di-comment di production)
        echo "<pre>Query error:\n" . mysqli_error($conn) . "\nQuery: $data</pre>";
        return []; // Kembalikan array kosong agar tidak error di pemanggil
    }

    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}
