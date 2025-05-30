<?php

// menjalankan session
session_start();

date_default_timezone_set('Asia/Jakarta');


// Tendang user yang belum login (cheat url)
if (!isset($_SESSION["login"])) {
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
    header("Location: home.php");
    exit;
}


$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $qty = $_POST['qty'];
    $harga = $_POST['harga'];
    $status = ($qty > 0) ? 'Tersedia' : 'Tidak Tersedia';
    $action = $_POST['action'];

    if ($action == 'add') {
        $sql = "INSERT INTO tb_barang (name, qty, harga, status) VALUES ('$name', '$qty', '$harga', '$status')";
        if ($conn->query($sql) === TRUE) {
            $message = 'Barang berhasil ditambahkan.';
        } else {
            echo "Error: " . $conn->error;
        }
    } elseif ($action == 'edit' && isset($_POST['id'])) {
        $id = $_POST['id'];
        $query = "SELECT qty FROM tb_barang WHERE id='$id'";
        $result = $conn->query($query);
        $oldData = $result->fetch_assoc();
        $qty_lama = $oldData['qty'];
        $selisih = $qty - $qty_lama;
        $sql = "UPDATE tb_barang SET name='$name', qty='$qty', harga='$harga', status='$status' WHERE id='$id'";
        $conn->query($sql);
        $tanggal = date('Y-m-d H:i:s');
        if ($selisih > 0) {
            $conn->query("INSERT INTO tb_barang_masuk (id_barang, jumlah, tanggal) VALUES ('$id', '$selisih', '$tanggal')");
        } elseif ($selisih < 0) {
            $jumlah_keluar = abs($selisih);
            $conn->query("INSERT INTO tb_barang_keluar (id_barang, jumlah, tanggal) VALUES ('$id', '$jumlah_keluar', '$tanggal')");
        }
        $message = 'Barang berhasil diperbarui.';
    }
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM tb_barang WHERE id='$id'";
    if ($conn->query($sql) === TRUE) {
        $message = 'Barang berhasil dihapus.';
    }
}

$editData = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $res = $conn->query("SELECT * FROM tb_barang WHERE id = '$id'");
    $editData = $res->fetch_assoc();
}

$sql = "SELECT * FROM tb_barang ORDER BY id DESC";
$result = $conn->query($sql);
if (!$result) {
    die("Query error: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Itemize - Itemize Cloud</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <nav>
        <div class="logo"><span>●</span><b> Itemize <span>Cloud</span></b></div>
        <ul class="nav-links">
            <li><a href="home.php">Home</a></li>
            <?php if ($row['role'] == 'admin'): ?>
                <li><a href="#" class="active">Itemize</a></li>
                <li><a href="laporan.php">Laporan</a></li>
                <li><a href="user.php">User</a></li>
            <?php endif; ?>
            <li><a href="../logout.php">Logout</a></li>
        </ul>
    </nav>

    <header class="hero">
        <h1>Manage Your<br>Item Inventory</h1>
        <p>Edit, delete, or add new items easily through this dashboard.</p>
    </header>

    <main>
        <h2>Item List</h2>

        <?php if ($message): ?>
            <div style="padding: 10px; background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; margin-bottom: 20px;">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="POST" style="margin-bottom: 20px;">
            <input type="hidden" name="action" value="<?= $editData ? 'edit' : 'add' ?>">
            <input type="hidden" name="id" value="<?= $editData['id'] ?? '' ?>">
            <input type="text" name="name" placeholder="Nama Barang" required value="<?= htmlspecialchars($editData['name'] ?? '') ?>">
            <input type="number" name="qty" placeholder="QTY" required value="<?= $editData['qty'] ?? '' ?>">
            <input type="text" name="harga" placeholder="Harga" required value="<?= $editData['harga'] ?? '' ?>">
            <button type="submit"><?= $editData ? 'Perbarui Item' : 'Tambah Item' ?></button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>QTY</th>
                    <th>Harga</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= $row['qty'] ?></td>
                        <td>Rp. <?= number_format((int)$row['harga'], 0, ',', '.') ?></td>
                        <td><?= $row['status'] ?></td>
                        <td>
                            <a href="?edit=<?= $row['id'] ?>" class="btn-modern edit">Edit</a>
                            <a href="?delete=<?= $row['id'] ?>" class="btn-modern delete" onclick="return confirm('Hapus item ini?')">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </main>
</body>

</html>

<?php $conn->close(); ?>