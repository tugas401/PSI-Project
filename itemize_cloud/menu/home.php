<?php

// menjalankan session
session_start();

// Tendang user yang belum login (cheat url)
if(!isset($_SESSION["login"])){
    header("Location: ../login.php");
    exit;
}

// Ambil id user yang login
require '../function/functions.php';
$userId = $_SESSION['id'];  // ID user yang login

// Ambil role user dari database
$result = mysqli_query($conn, "SELECT role FROM user WHERE id = $userId");
$row = mysqli_fetch_assoc($result);

// Cek apakah role ada, jika tidak ada, redirect ke home
if (!$row) {
    header("Location: home.php");
    exit;
}

// Ambil semua data barang dari database
$sql = "SELECT * FROM tb_barang";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Itemize Cloud</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- menu bar atas -->
    <nav>
        <div class="logo"><span>●</span><b> Itemize <span>Cloud</span></b></div>
        <ul class="nav-links">
            <li><a href="#" class="active">Home</a></li>
            <?php if ($row['role'] == 'admin'): ?>
                <li><a href="itemize.php">Itemize</a></li>
                <li><a href="laporan.php">Laporan</a></li>
                <li><a href="user.php">User</a></li>
            <?php endif; ?>
            <li><a href="../logout.php">Logout</a></li>
        </ul>
    </nav>

    <!-- header -->
    <header class="hero">
        <h1>Itemize your <br> stuff now</h1>
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
    </header>

    <!-- tabel list barang yang tersedia -->
    <table>
        <!-- Menu tabel -->
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>QTY</th>
                <th>Harga</th>
                <th>Status</th>
            </tr>
        </thead>

        <!-- Isi tabel -->
        <tbody id="itemTable">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['id']) ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['qty']) ?></td>
                    <td>Rp. <?= number_format((int)$row['harga'], 0, ',', '.') ?></td>
                    <td><?= htmlspecialchars($row['status']) ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="5">Data barang tidak ditemukan.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>

      <script src="script.js"></script>
</body>
</html>

<?php
$conn->close();
?>