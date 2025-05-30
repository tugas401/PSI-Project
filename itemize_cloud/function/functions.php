<?php
// koneksi database
$conn = mysqli_connect("localhost", "root", "", "itemize_cloud");


// fungsi registrasi
function registrasi($data){
    global $conn;

    // aturan + input username ke database
    $username = strtolower(stripslashes($data["username"]));

    // input register-password ke database
    $password = mysqli_real_escape_string($conn, $data["password"]);
    
    // input confirm-password ke database
    $password2 = mysqli_real_escape_string($conn, $data["password2"]);

    // cek jika username sudah ada atau belum
    $result = mysqli_query($conn, "SELECT username FROM user WHERE username = '$username'");

    // jika username sama
    if(mysqli_fetch_assoc($result)){
        echo "<script>
            alert('username yang dipilih sudah terdaftar!');
        </script>";
        return false; // <-- berhentikan fungsi
    }

    // cek jika password salah
    if($password !== $password2){
        echo "<script>
            alert('konfirmasi password tidak sesuai!');
        </script>";
        return false; // <-- berhentikan fungsi
    }

    // enkripsi password
    $password = password_hash($password, PASSWORD_DEFAULT);

    // tambahkan userbaru ke database
    mysqli_query($conn, "INSERT INTO user (username, password, role) VALUES('$username', '$password', 'guest')");

    // untuk menghasilkan angka 1 jika berhasil atau -1 jika gagal
    return mysqli_affected_rows($conn);
}

?>