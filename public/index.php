<?php
require '../backend/global.php';
require '../backend/function/function_problem.php';
$users = queryData("SELECT * FROM tbl_problems ORDER BY RAND() LIMIT 3");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <header>
        <h1>🧠 Bisik.In</h1>
        <p>Ruang ekspresi anonim, kamu bebas meluapkan perasaan tanpa takut dihakimi.</p>
    </header>

    <main>
        <!-- Form Kirim Masalah -->
        <section class="submit-form">
            <h2>📝 Tulis yang Tersimpan</h2>
            <form action="" method="post">
                <textarea name="content" placeholder="Tulis masalahmu di sini..." required></textarea>
                <button type="submit" name="kirim">Kirim</button>
            </form>
        </section>
        <section class="problem-list">
            <h2>📝 Cerita Tanpa Nama</h2>
            <?php foreach ($users as $user) :
            ?>
                <?php $comments = getComments($user['id']);
                ?>


                <div class="problem">
                    <p><strong><?= $user['content'] ?></strong></p>
                    <span style="font-size: 12px;">📅 <?= date('d M Y H:i:s', strtotime($user['created_at'])) ?></span>

                    <div class="actions reactions" data-post-id="<?= $user['id'] ?>">
                        <div class="reaction-trigger">
                            <span class="react reaction-item main-like" data-emoji="like">
                                👍❤️😂😢😠 <span class="count"><?= $reactions_data[$user['id']]['like'] ?? 0 ?></span>
                            </span>

                            <!-- Popup emoji -->
                            <div class="reaction-popup">
                                <span class="react reaction-choice" data-emoji="like">👍</span>
                                <span class="react reaction-choice" data-emoji="love">❤️</span>
                                <span class="react reaction-choice" data-emoji="laugh">😂</span>
                                <span class="react reaction-choice" data-emoji="sad">😢</span>
                                <span class="react reaction-choice" data-emoji="angry">😠</span>
                            </div>
                        </div>

                        <!-- Sisa tombol -->
                        <span class="toggle-comments" style="cursor: pointer;" data-target="comments-<?= $user['id'] ?>">
                            💬 <?= count($comments) ?> komentar
                        </span>

                        <?php if ($_SESSION['user_token'] === $user['user_token']): ?>
                            <form method="POST" class="delete-form">
                                <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                <button type="submit" name="delete" class="delete-btn">Delete</button>
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
        <section style="margin-top: 40px; text-align: center;">
            <a href="feed.php" class="back-button">Lihat Semua Cerita 🔍</a>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 Problemownia | Dibuat dengan empati 💛</p>
    </footer>

    <?php
    require './js/views/script.php';


    if (isset($_POST['kirim'])) {
        if (addProblems($_POST) > 0) {
            echo '
                    <script type="text/javascript">
                        Swal.fire({
                        title: "Success!",
                        icon: "success",
                        draggable: true
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
                        text: "Something went wrong!"
                        }).then(function(isConfirm){
                            if(isConfirm){
                                window.location.replace("index.php");
                            }
                        });
                    </script>
                ';
        }
    }

    ?>



</body>

</html>
