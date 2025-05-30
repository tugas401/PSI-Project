<?php
session_start();

if (!isset($_SESSION["login"])) {
    header("Location: ../login.php");
    exit;
}

require '../function/functions.php';
$userId = $_SESSION['id'];
$result = mysqli_query($conn, "SELECT role FROM user WHERE id = $userId");
$row = mysqli_fetch_assoc($result);

if (!$row) {
    header("Location: home.php");
    exit;
}

$keyword = isset($_GET['search']) ? $_GET['search'] : '';

if (!empty($keyword)) {
    $sql = "SELECT * FROM tb_barang WHERE name LIKE '%" . mysqli_real_escape_string($conn, $keyword) . "%'";
} else {
    $sql = "SELECT * FROM tb_barang";
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Home - Itemize Cloud</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- Menu Navigasi -->
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

<!-- Header -->
<header class="hero">
    <h1>Dashboard Barang</h1>
    <p>Kelola dan pantau stok barang secara real-time.</p>
</header>

<!-- Main Content -->
<main>
    <h2>Daftar Barang Tersedia</h2>

    <!-- Form Pencarian -->
    <div class="search-container">
        <form action="" method="get">
            <input type="text" name="search" placeholder="Cari barang..." value="<?= htmlspecialchars($keyword) ?>">
            <button type="submit">🔍</button>
        </form>
    </div>

    <!-- Tabel Data Barang -->
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>QTY</th>
                <th>Harga</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody id="itemTable">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td data-label="ID"><?= htmlspecialchars($row['id']) ?></td>
                        <td data-label="Nama"><?= htmlspecialchars($row['name']) ?></td>
                        <td data-label="QTY"><?= htmlspecialchars($row['qty']) ?></td>
                        <td data-label="Harga">Rp. <?= number_format((int)$row['harga'], 0, ',', '.') ?></td>
                        <td data-label="Status"><?= htmlspecialchars($row['status']) ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">Data barang tidak ditemukan.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</main>

</body>
</html>

<?php
$conn->close();
?>