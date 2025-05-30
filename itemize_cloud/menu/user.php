<?php

// menjalankan session
session_start();

// Tendang user yang belum login (cheat url)
if(!isset($_SESSION["login"])){
    header("Location: ../login.php");
    exit;
}

// Ambil role dari database berdasarkan id user yang login
require '../function/functions.php';
$userId = $_SESSION['id']; // ID user yang login
$result = mysqli_query($conn, "SELECT role FROM user WHERE id = $userId");
$row = mysqli_fetch_assoc($result);

// Ambil role user dari database
$result = mysqli_query($conn, "SELECT role FROM user WHERE id = $userId");
$row = mysqli_fetch_assoc($result);

// Jika gagal ambil data (misalnya user tidak ditemukan)
if (!$row) {
    echo "Gagal mengambil data user.";
    exit;
}

// Cek apakah user adalah admin atau guest
if ($row['role'] == 'guest' && basename($_SERVER['PHP_SELF']) != 'home.php') {
    // Redirect jika guest mencoba mengakses halaman selain home.php
    header("Location: home.php");
    exit;
}

// ganti username
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_username'])) {
$usernameBaru = htmlspecialchars($_POST["username_baru"]);
mysqli_query($conn, "UPDATE user SET username = '$usernameBaru' WHERE id = $userId");
// notifikasi username berhasil diubah
$_SESSION['message'] = "Username berhasil diubah!";
header("Location: user.php");
exit;
}

// password
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_password'])) {
$newPass = $_POST["new_password"];
$confirmPass = $_POST["confirm_password"];
  if ($newPass === $confirmPass) {
      $hashedPass = password_hash($newPass, PASSWORD_DEFAULT);
      mysqli_query($conn, "UPDATE user SET password = '$hashedPass' WHERE id = $userId");
      // notifikasi password berhasil diubah
      $_SESSION['message'] = "Password berhasil diubah!";
      header("Location: user.php");
      exit;
  } else {
      echo "<script>alert('Password tidak cocok!');</script>";
  }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>User - Itemize Cloud</title>
  <link rel="stylesheet" href="style.css">

  <!-- menampilkan username role admin yang hanya ada satu -->
  <?php $resultUser = mysqli_query($conn, "SELECT username FROM user WHERE id = $userId AND role = 'admin'"); $userData = mysqli_fetch_assoc($resultUser); $usernameSaatIni = $userData['username']; ?>
</head>
<body>

    <!-- menu bar atas -->
  <nav>
    <div class="logo"><span>●</span><b> Itemize <span>Cloud</span></b></div>
    <ul class="nav-links">
      <li><a href="home.php">Home</a></li>
            <?php if ($row['role'] == 'admin'): ?>
                <li><a href="itemize.php">Itemize</a></li>
                <li><a href="laporan.php">Laporan</a></li>
                <li><a href="#" class="active">User</a></li>
            <?php endif; ?>
      <li><a href="../logout.php">Logout</a></li>
    </ul>
  </nav>


    <!-- header -->
    <header class="hero">
        <h1>Profil Pengguna</h1>
        <p>Informasi akun dan pengelolaan user guest.</p>
    </header>

    <main>

        <!-- Style Notifikasi pesan -->
        <?php if (isset($_SESSION['message'])): ?>
            <div style="padding: 10px; background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; margin-bottom: 20px;">
                <?= htmlspecialchars($_SESSION['message']) ?>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        

        <!-- Ganti Username -->
        <section class="form-section">
        <h2>Ganti Username Anda</h2>
        <form method="POST"> 
          <label>Username saat ini</label> 
          <input type="email" readonly value="<?= $usernameSaatIni ?>"> 
          <input type="text" name="username_baru" placeholder="Username Baru" required> 
          <button type="submit" name="update_username">Ganti Username</button> 
        </form>
        </section>

        <!-- Ganti Password -->
        <section class="form-section">
          <h2>Ganti Password Anda</h2>
          <form method="POST"> 
            <input type="password" name="new_password" placeholder="Password Baru" required> 
            <input type="password" name="confirm_password" placeholder="Konfirmasi Password" required> 
            <button type="submit" name="update_password">Ganti Password</button> 
          </form>
        </section>
</body>
</html>
