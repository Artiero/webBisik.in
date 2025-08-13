<?php
session_start();
header('Content-Type: application/json');

// Pastikan error tidak tampil dalam output (agar JSON tidak rusak)
error_reporting(0);

// Load koneksi & fungsi global
require '../backend/global.php';

// Jika koneksi gagal
if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Ambil data POST
$problem_id = isset($_POST['problem_id']) ? intval($_POST['problem_id']) : 0;
$emoji = isset($_POST['emoji']) ? trim($_POST['emoji']) : '';
$session_id = session_id();

// Validasi input
$allowed_emojis = ['like', 'love', 'laugh', 'sad', 'angry'];
if ($problem_id <= 0 || !in_array($emoji, $allowed_emojis)) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

// Cek apakah sudah ada reaction dari session ini
$stmt = $conn->prepare("SELECT id FROM tbl_reactions WHERE problem_id = ? AND session_id = ?");
$stmt->bind_param("is", $problem_id, $session_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Update reaction yang ada
    $update = $conn->prepare("UPDATE tbl_reactions SET emoji = ?, created_at = NOW() 
                            WHERE problem_id = ? AND session_id = ?");
    $update->bind_param("sis", $emoji, $problem_id, $session_id);
    $update->execute();
} else {
    // Insert reaction baru
    $insert = $conn->prepare("INSERT INTO tbl_reactions (problem_id, emoji, session_id) VALUES (?, ?, ?)");
    $insert->bind_param("iss", $problem_id, $emoji, $session_id);
    $insert->execute();
}

// Hitung jumlah reaction per emoji
$counts = [];
$count_query = $conn->prepare("SELECT emoji, COUNT(*) as total 
                                FROM tbl_reactions 
                                WHERE problem_id = ? 
                                GROUP BY emoji");
$count_query->bind_param("i", $problem_id);
$count_query->execute();
$count_result = $count_query->get_result();

while ($row = $count_result->fetch_assoc()) {
    $counts[$row['emoji']] = (int) $row['total'];
}

// Hitung total semua reaction
$total_all_query = $conn->prepare("SELECT COUNT(*) as total_all FROM tbl_reactions WHERE problem_id = ?");
$total_all_query->bind_param("i", $problem_id);
$total_all_query->execute();
$total_all_result = $total_all_query->get_result();
$total_all_row = $total_all_result->fetch_assoc();
$total_all = (int) $total_all_row['total_all'];

// Balikkan hasil
echo json_encode([
    'success' => true,
    'counts' => $counts,
    'total_all' => $total_all
]);
exit;
