<?php
require '../backend/global.php';
require '../backend/function/function_problem.php';
$users = queryData("SELECT * FROM tbl_problems");

$reactions_data = [];
foreach ($users as $user) {
    $res = mysqli_query($conn, "
        SELECT emoji, COUNT(*) as total 
        FROM tbl_reactions 
        WHERE problem_id = {$user['id']} 
        GROUP BY emoji
    ");
    $temp = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $temp[$row['emoji']] = $row['total'];
    }
    $reactions_data[$user['id']] = $temp;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Problemownia Feed</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

    <header>
        <h1>Problemownia</h1>
        <p>Lihat dan dukung masalah dari orang lain secara anonim</p>
    </header>

    <main>
        <section class="problem-list">
            <h2>📝 Masalah Terbaru</h2>
            <?php foreach ($users as $user) :
            ?>
                <?php $comments = getComments($user['id']);
                ?>


                <div class="problem">
                    <p><strong><?= $user['content'] ?></strong></p>
                    <span>📅 <?= date('d M Y H:i:s', strtotime($user['created_at'])) ?></span>

                    <div class="actions" data-post-id="<?= $user['id'] ?>">
                        <span class="react" data-emoji="like">
                            👍 <span class="count"><?= $reactions_data[$user['id']]['like'] ?? 0 ?></span>
                        </span>
                        <span class="react" data-emoji="love">
                            ❤️ <span class="count"><?= $reactions_data[$user['id']]['love'] ?? 0 ?></span>
                        </span>
                        <span class="react" data-emoji="laugh">
                            😂 <span class="count"><?= $reactions_data[$user['id']]['laugh'] ?? 0 ?></span>
                        </span>
                        <span class="react" data-emoji="sad">
                            😢 <span class="count"><?= $reactions_data[$user['id']]['sad'] ?? 0 ?></span>
                        </span>
                        <span class="react" data-emoji="angry">
                            😠 <span class="count"><?= $reactions_data[$user['id']]['angry'] ?? 0 ?></span>
                        </span>


                        <!-- Komentar trigger -->
                        <span class="toggle-comments" style="cursor: pointer;" data-target="comments-<?= $user['id'] ?>">
                            💬 <?= count($comments) ?> komentar
                        </span>

                        <?php if ($_SESSION['user_token'] === $user['user_token']): ?>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                <button type="submit" name="delete"
                                    style="background-color: #ff6b6b; color: white; border: none; padding: 5px 10px; border-radius: 8px; cursor: pointer; transition: background-color 0.3s ease;">
                                    Delete
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>

                    <!-- Komentar wrapper yang bisa ditampilkan/sembunyikan -->
                    <div class="comments" id="comments-<?= $user['id'] ?>" style="display: none; margin-top: 10px;">
                        <h4>Komentar:</h4>
                        <?php if (count($comments) > 0): ?>
                            <ul style="list-style: none; padding-left: 0;">
                                <?php foreach ($comments as $comment): ?>
                                    <li style="margin-bottom: 8px; background: #f1f1f1; padding: 5px; border-radius: 8px;">
                                        <?= htmlspecialchars($comment['comment']) ?> <br>
                                        <small style="color: #888;">🕒 <?= date('d M Y H:i:s', strtotime($comment['created_at'])) ?></small>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p>Belum ada komentar.</p>
                        <?php endif; ?>

                        <!-- Form komentar -->
                        <form method="POST" style="margin-top: 10px;">
                            <input type="hidden" name="id" value="<?= $user['id'] ?>">
                            <input type="text" name="comment" placeholder="Tulis komentar..." required
                                style="width: 70%; padding: 5px;">
                            <button type="submit" name="add_comment"
                                style="padding: 5px 10px; background-color: #00b4d8; color: white; border: none; border-radius: 5px;">
                                Kirim
                            </button>
                        </form>
                    </div>
                </div>

            <?php
            endforeach;
            ?>

        </section>
        <div style="text-align: center; margin-top: 30px;">
            <a href="index.php">
                <button
                    style="padding: 10px 20px; border: none; background-color: #0077b6; color: white; border-radius: 8px; cursor: pointer;">Kirim
                    Masalah Baru</button>
            </a>
        </div>
    </main>
    <footer>
        &copy; 2025 Problemownia — Ruang Aman Berbagi Masalah
    </footer>

    <?php
    require './js/views/script.php';

    if (isset($_POST['delete'])) {
        // var_dump($_POST);
        if (deleteProblem($_POST['id']) > 0) {
            echo '
            <script type="text/javascript">
                Swal.fire({
                    title: "Deleted!",
                    text: "Your problem has been removed.",
                    icon: "success"
                }).then(function(isConfirm){
                    if(isConfirm){
                        window.location.replace("feed.php");
                    }
                });
            </script>
        ';
        } else {
            echo '
            <script type="text/javascript">
                Swal.fire({
                    icon: "error",
                    title: "Oops...",
                    text: "You can only delete your own content or an error occurred."
                }).then(function(isConfirm){
                    if(isConfirm){
                        window.location.replace("feed.php");
                    }
                });
            </script>
        ';
        }
    }

    if (isset($_POST['add_comment'])) {
        $problem_id = $_POST['id'];
        $comment = trim($_POST['comment']);
        $session_id = session_id();

        if (isset($_POST['add_comment'])) {
            $problem_id = $_POST['id'];
            $comment = trim($_POST['comment']);
            $session_id = session_id();

            if (!empty($comment)) {
                if (addComment($problem_id, $comment, $session_id)) {
                    echo '
                    <script type="text/javascript">
                        Swal.fire({
                        title: "Comment Added!",
                        text: "Your comment has been successfully posted.",
                        icon: "success"
                    }).then(function(){
                        window.location.replace("feed.php");
                    });
                </script>';
                } else {
                    echo '
                <script type="text/javascript">
                    Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        text: "Failed to add comment. Please try again."
                    }).then(function(){
                        window.location.replace("feed.php");
                    });
                </script>';
                }
            } else {
                echo '
            <script type="text/javascript">
                Swal.fire({
                    icon: "warning",
                    title: "Empty Comment",
                    text: "Please write something before posting."
                }).then(function(){
                    window.location.replace("feed.php");
                });
            </script>';
            }
        }
    }



    ?>


</body>

</html>