<?php

require 'function/functions.php';

// ketika tombol register di-click 
if(isset($_POST["register"])){
    
    // registrasi jika user berhasil masuk -> untuk Function
    if(registrasi($_POST) > 0){
        echo "<script>
            alert('user baru berhasil ditambahkan!');
        </script>";
    } else {
        echo mysqli_error($conn);
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Itemize</title>
    <link rel="stylesheet" href="style_logres.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap">
</head>
<body>
<div class="container">
        <!-- panel kiri -->
        <div class="left-panel">
            <h2>Register.</h2>
            <p>Welcome to itemize! Make your account and start itemize</p>

            <form action="" method="post">

                <!-- username -->
                <input 
                type="text"
                name="username"
                id="username" 
                placeholder="Username" 
                onclick="clearPlaceholder(this)"
                required>
                
                <!-- password -->
                <input 
                type="password"
                name="password" 
                id="password" 
                placeholder="Password" 
                onclick="clearPlaceholder(this)"
                required>
                
                <!-- password confirm -->
                <input 
                type="password"
                name="password2" 
                id="password2" 
                placeholder="Confirm Password" 
                onclick="clearPlaceholder(this)"
                required>
                
                <!-- button Register-->
                <button type="submit" name="register">Register</button>
            </form>
        </div>
            
        <!-- panel kanan -->
        <div class="right-panel">
            <div class="overlay">
                <h1>Itemize your<br>stuff now</h1>
                <p>if you already have an account, click here to login</p>

                <!-- button Login-->
                <button class="outline" onclick="goToLogin()">
                    <a href="login.php">Login →</a>
                </button>
            </div>
        </div>
    </div>
</body>
</html>