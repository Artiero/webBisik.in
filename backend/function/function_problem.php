<?php
require '../backend/connection.php';

function addProblems($data)
{
    global $conn;

    // Ambil session_id dari $_SESSION
    $session_id = mysqli_real_escape_string($conn, $_SESSION['user_token']);
    $content = mysqli_real_escape_string($conn, $data['content']);

    // Masukkan session_id ke dalam query
    mysqli_query($conn, "INSERT INTO tbl_problems VALUES (NULL, '$session_id', '$content', CURRENT_TIMESTAMP)");

    return mysqli_affected_rows($conn);
}

function deleteProblem($id)
{
    global $conn;

    $user_token = $_SESSION['user_token'];
    $id = intval($id);

    mysqli_query($conn, "DELETE FROM tbl_problems WHERE id = $id AND user_token = '$user_token'");
    return mysqli_affected_rows($conn);
}

function addComment($problem_id, $comment, $session_id)
{
    global $conn;

    $problem_id = intval($problem_id);
    $comment = mysqli_real_escape_string($conn, $comment);
    $session_id = mysqli_real_escape_string($conn, $session_id);

    $query = "INSERT INTO tbl_comments (problem_id, comment, session_id) VALUES ($problem_id, '$comment', '$session_id')";
    mysqli_query($conn, $query);

    return mysqli_affected_rows($conn);
}




function getComments($problem_id)
{
    global $conn;
    $problem_id = intval($problem_id);
    $result = mysqli_query($conn, "SELECT * FROM tbl_comments WHERE problem_id = $problem_id ORDER BY created_at DESC");

    $comments = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $comments[] = $row;
    }
    return $comments;
}


