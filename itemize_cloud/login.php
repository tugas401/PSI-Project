<?php
// menjalankan session
session_start();

// panggil file 'functions' database
require 'function/functions.php';

// verifikasi cookie
if(isset($_COOKIE['id']) && isset($_COOKIE['key'])){
    $id = $_COOKIE['id'];
    $key = $_COOKIE['key'];

    // ambil username berdasarkan id
    $result = mysqli_query($conn, "SELECT username FROM user WHERE id = $id");
    $row = mysqli_fetch_assoc($result);

    // verifikasi cookie dan username. 'key' = username yang di-enkripsi
    if($key === hash('sha256', $row['username'])){
        // jika sesi login masih ada, pindahkan ke menu home dibawah ↓↓↓ 
        $_SESSION['login'] = true; 
    }
}

// jika user sebelumnya sudah login
if(isset($_SESSION["login"])){
    header("Location: menu/home.php");
    exit;
}

// menangkap data ketika tombol login di-klik
if (isset($_POST["login"])){
    $username = $_POST["username"];
    $password = $_POST["password"];

    // cek jika ada username terdaftar di database
    $result = mysqli_query($conn, "SELECT * FROM user WHERE username = '$username'");

    // cek username. Jika 1 ada, jika 0 tidak ada.
    if (mysqli_num_rows($result) === 1 ){
        // cek passwordnya 
        $row = mysqli_fetch_assoc($result);

        // fungsi mengubah hash jadi text/string
        if(password_verify($password, $row["password"])){

            // set session
            $_SESSION["login"] = true; 
            $_SESSION['id'] = $row['id']; // Menyimpan ID pengguna yang login

            // Cookie aktif saat tombol 'remember' diceklist
            if(isset($_POST['remember'])){

                // buat keamanan cookie (enkripsi)
                setcookie('id', $row['id'], time() + (7 * 24 * 60 * 60)); // 7 hari
                setcookie('key', hash('sha256', $row['username']), time() + (7 * 24 * 60 * 60)); // 7 hari
            }

            // arahkan user ke halaman dashboard 
            header("Location: menu/home.php"); 
            exit; // <-- program berhenti
        }
    }

    // variabel notif kesalahan. Lanjutan ada di bawah ↓↓↓
    $error = true;
}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Guest Login | Itemize</title>
        <link rel="stylesheet" href="style_logres.css" />
        <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap"
        rel="stylesheet"
        />
    </head>
    <body>
        <div class="container">
            <!-- panel kiri -->
            <div class="left-panel">
                <!-- Form method post -->
                <form action="" method="post">
                    <h2>Login.</h2>
                    <p>Welcome to itemize! Please login to your account</p>

                    <!-- username -->
                    <input
                    type="text"
                    name="username"
                    id="username"
                    placeholder="Username"
                    onclick="clearPlaceholder(this)"
                    />
                    
                    <!-- password -->
                    <input
                    type="password"
                    name="password"
                    id="password"
                    placeholder="Password"
                    onclick="clearPlaceholder(this)"
                    />

                    <!-- notifikasi jika password / username salah -->
                    <?php if(isset($error)) : ?>
                        <p style="
                            color: red;
                            font-style: italic;
                            ">
                            username / password salah
                        </p>
                    <?php endif;?>

                    <!-- remember me -->
                    <div class="remember-me" >
                        <input 
                        type="checkbox"
                        name="remember"
                        id="remember"
                        />
                        <label for="remember">Remember me</label>
                    </div>
                    
                    <!-- button Login-->
                    <button type="submit" name="login">Login</button>
                </form>
            </div>
                
            <!-- panel kanan -->
            <div class="right-panel">
                <div class="overlay">
                    <h1>Itemize your<br>stuff now</h1>
                    <p>if you don't have an account yet, click here to register</p>
                    
                    <!-- button Register-->
                    <button class="outline"onclick="goToRegister()">
                        <a href="register.php">
                            Register →
                        </a>
                    </button>
                </div>
            </div>
        </div>
        <script src="script.js"></script>
    </body>
</html>