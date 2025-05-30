<?php

// menjalankan session
session_start();

date_default_timezone_set('Asia/Jakarta');


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

// Cek apakah user adalah admin atau guest
if ($row['role'] == 'guest' && basename($_SERVER['PHP_SELF']) != 'home.php') {
    // Redirect jika guest mencoba mengakses halaman selain home.php
    header("Location: home.php");
    exit;
}

$sql = "
    SELECT bm.tanggal, b.name, bm.jumlah AS qty, 'Masuk' AS tipe
    FROM tb_barang_masuk bm
    JOIN tb_barang b ON bm.id_barang = b.id
    UNION
    SELECT bk.tanggal, b.name, bk.jumlah AS qty, 'Keluar' AS tipe
    FROM tb_barang_keluar bk
    JOIN tb_barang b ON bk.id_barang = b.id
    ORDER BY tanggal DESC
";

$result = $conn->query($sql); // Pastikan koneksi $conn ada di sini
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Laporan - Itemize Cloud</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- menu bar atas -->
    <nav>
        <div class="logo"><span>●</span><b> Itemize <span>Cloud</span></b></div>
        <ul class="nav-links">
            <li><a href="home.php">Home</a></li>
            <?php if ($row['role'] == 'admin'): ?>
                <li><a href="itemize.php">Itemize</a></li>
                <li><a href="#" class="active">Laporan</a></li>
                <li><a href="user.php">User</a></li>
            <?php endif; ?>
            <li><a href="../logout.php">Logout</a></li>
        </ul>
    </nav>


    <!-- header -->
    <header class="hero">
        <h1>Laporan Barang</h1>
        <p>Ringkasan barang masuk dan keluar untuk pemantauan stok.</p>
    </header>

    <main>
        <h2>Laporan Barang Masuk & Keluar</h2>
        <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Waktu</th>
                <th>Nama Barang</th>
                <th>Jumlah</th>
                <th>Tipe</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td>
                    <?php
                    $datetime = new DateTime($row['tanggal'], new DateTimeZone('Asia/Jakarta'));
                    echo $datetime->format('d-m-Y');
                    ?>
                </td>
                <td>
                    <?php
                    echo $datetime->format('H:i:s');
                    ?>
                </td>
                <td><?php echo $row['name']; ?></td>
                <td><?php echo $row['qty']; ?></td>
                <td><?php echo $row['tipe']; ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </main>
</body>
</html>
