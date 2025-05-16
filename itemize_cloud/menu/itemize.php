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

// Cek apakah user adalah admin atau guest
if ($row['role'] == 'guest' && basename($_SERVER['PHP_SELF']) != 'home.php') {
    // Redirect jika guest mencoba mengakses halaman selain home.php
    header("Location: home.php");
    exit;
}



// Proses tambah atau edit data item
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $name = $_POST['name'];
    $qty = $_POST['qty'];
    $harga = $_POST['harga'];
    $status = $_POST['status'];
    $action = $_POST['action'];

    if ($action == 'add') {
        // Tambah barang baru
        $sql = "INSERT INTO tb_barang (name, qty, harga, status) 
                VALUES ('$name', '$qty', '$harga', '$status')";
        
        if ($conn->query($sql) === TRUE) {
        
        } else {
            echo "Error: " . $conn->error;
        }
    } elseif ($action == 'edit' && isset($_POST['id'])) {
        // Edit data barang
        $id = $_POST['id'];

        // Ambil data lama sebelum update
        $query = "SELECT qty FROM tb_barang WHERE id='$id'";
        $result = $conn->query($query);
        $oldData = $result->fetch_assoc();
        $qty_lama = $oldData['qty'];

        // Hitung selisih
        $selisih = $qty - $qty_lama;

        // Update data barang
        $sql = "UPDATE tb_barang SET name='$name', qty='$qty', harga='$harga', status='$status' WHERE id='$id'";
        $conn->query($sql);

        // Catat laporan berdasarkan perubahan
        $tanggal = date('Y-m-d');
        if ($selisih > 0) {
            // Barang Masuk
            $conn->query("INSERT INTO tb_barang_masuk (id_barang, jumlah, tanggal) VALUES ('$id', '$selisih', '$tanggal')");
        } elseif ($selisih < 0) {
            // Barang Keluar
            $jumlah_keluar = abs($selisih);
            $conn->query("INSERT INTO tb_barang_keluar (id_barang, jumlah, tanggal) VALUES ('$id', '$jumlah_keluar', '$tanggal')");
        }
    }
}

// Proses hapus data item
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM tb_barang WHERE id='$id'";
    $conn->query($sql);
}

// Ambil data barang dari database untuk ditampilkan
$sql = "SELECT * FROM tb_barang";

$result = $conn->query($sql);
if (!$result) {
    die("Query error: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Itemize - Itemize Cloud</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- menu bar atas -->
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

    <!-- header -->
    <header class="hero">
        <h1>Manage Your<br>Item Inventory</h1>
        <p>Edit, delete, or add new items easily through this dashboard.</p>
    </header>

    <main>
        <h2>Item List</h2>
        <form method="POST">
            <!-- Kategori -->
            <input type="hidden" name="action" id="action" value="add">
            <input type="hidden" name="id" id="id"> <!-- Hanya dipakai saat edit -->
            <input type="text" name="name" id="name" placeholder="Nama Barang" required>
            <input type="number" name="qty" id="qty" placeholder="QTY" required>
            <input type="text" name="harga" id="harga" placeholder="Harga" required>
            
            <!-- Status barang -->
            <select name="status" id="status">
                <option value="Tersedia">Tersedia</option>
                <option value="Tidak Tersedia">Tidak Tersedia</option>
            </select>

            <!-- tombol submit -->
            <button type="submit" id="submitBtn">Tambah Item</button>
        </form>

        <!-- Tabel data barang -->
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
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo $row['qty']; ?></td>
                        <td><?php echo $row['harga']; ?></td>
                        <td><?php echo $row['status']; ?></td>
                        <td>
                            <button class="button_style" type="button" onclick="editItem(
                                <?php echo $row['id']; ?>,

                                '<?php echo addslashes($row['name']); ?>',

                                <?php echo $row['qty']; ?>,

                                '<?php echo $row['harga']; ?>',

                                '<?php echo $row['status']; ?>'

                            )">Edit</button>

                            <a href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Hapus item ini?')">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </main>

    <script> function editItem(id, name, qty, harga, status) {
            document.getElementById('id').value = id;
            document.getElementById('name').value = name;
            document.getElementById('qty').value = qty;
            document.getElementById('harga').value = harga;
            document.getElementById('status').value = status;
            document.getElementById('action').value = 'edit';
            document.getElementById('submitBtn').textContent = 'Perbarui Item';
        }
    </script>

</body>
</html>

<?php $conn->close(); ?>