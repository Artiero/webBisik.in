<?php
require '../backend/global.php';
require '../backend/function/function_problem.php';
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
        <h1>🧠 Problemownia</h1>
        <p>Bagikan masalahmu secara anonim. Dapatkan dukungan tanpa penghakiman.</p>
    </header>

    <main>
        <!-- Form Kirim Masalah -->
        <section class="submit-form">
            <h2>📝 Kirim Masalah</h2>
            <form action="" method="post">
                <textarea name="content" placeholder="Tulis masalahmu di sini..." required></textarea>
                <button type="submit" name="kirim">Kirim</button>
            </form>
        </section>
        <section style="margin-top: 40px; text-align: center;">
            <a href="feed.php" class="back-button">Lihat Semua Masalah 🔍</a>
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