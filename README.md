# Praktikum 8: PHP dan Database MySQL (CRUD Sederhana)
Nama : Reynaldi Nugraha Putra <br>
NIM  : 312410278 <br>
Kelas : TI.24.A.3 <br>
Matakuliah : Pemrograman Web Pert 10 <br>

### I. Membuat Database MySql/MariaDB Menggunakan CMD
- ``cd C:\xampp\mysql\bin``
- ``mysql -u root``
- ``create database latihan1;``
- ``use latihan1;``
Output:
<p align="center">
<img src="img/database1.png" width="70%">
</p>
<br>

A. Membuat Struktur Tabel.
Tabel utama yang digunakan adalah data_barang. Tabel ini dibuat dengan perintah SQL berikut:
 ```
CREATE TABLE data_barang ( 
     id_barang int(10) auto_increment Primary Key, 
     kategori varchar(30), 
     nama varchar(30), 
     gambar varchar(100), 
     harga_beli decimal(10,0), 
     harga_jual decimal(10,0), 
     stok int(4) 
    );
```

B. Memasukkan beberapa data awal untuk menguji fungsi Read dan Update.
```
   INSERT INTO data_barang (kategori, nama, gambar, harga_beli, harga_jual, stok)
    VALUES ('Elektronik', 'HP Samsung Android', 'hp_samsung.jpg', 2000000, 2400000, 5),
    ('Elektronik', 'HP Xiaomi Android', 'hp_xiaomi.jpg', 1000000, 1400000, 5),
    ('Elektronik', 'HP OPPO Android', 'hp_oppo.jpg', 1800000, 2300000, 5);
```

### II. Koneksi Database
Aplikasi menggunakan database latihan1. Konfigurasi koneksi diatur dalam koneksi.php.
file: `koneksi.php`
```
<?php 
$host = "localhost"; 
$user = "root"; 
$pass = ""; 
$db   = "latihan1"; 
 
$conn = mysqli_connect($host, $user, $pass, $db); 
if ($conn == false) 
{ 
    echo "Koneksi ke server gagal."; 
    die(); 
} else { 
    echo "Koneksi berhasil.";
} 
?> 
```
Output: 
img

### A. READ: Menampilkan Data (index.php)
File ini berfungsi untuk menampilkan semua data dari tabel data_barang dalam bentuk tabel HTML.
```
<?php
include("koneksi.php");
$sql = 'SELECT * FROM data_barang ORDER BY id_barang DESC';
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Barang</title>
    <link href="style.css" rel="stylesheet" type="text/css" />
</head>
<body>
    <div class="container">
        <h1>Data Barang</h1>
        <div class="toolbar">
            <a class="btn" href="tambah.php">Tambah Barang</a>
        </div>
        <div class="main">
            <table>
                <tr>
                    <th>Gambar</th>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th>Harga Beli</th>
                    <th>Harga Jual</th>
                    <th>Stok</th>
                    <th>Aksi</th>
                </tr>
                <?php if($result && mysqli_num_rows($result) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td>
                                <?php if(!empty($row['gambar']) && file_exists($row['gambar'])): ?>
                                    <img class="thumb" src="<?php echo htmlspecialchars($row['gambar']); ?>" alt="<?php echo htmlspecialchars($row['nama']); ?>">
                                <?php else: ?>
                                    <span class="noimg">-</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($row['nama']); ?></td>
                            <td><?php echo htmlspecialchars($row['kategori']); ?></td>
                            <td><?php echo number_format($row['harga_beli'],0,',','.'); ?></td>
                            <td><?php echo number_format($row['harga_jual'],0,',','.'); ?></td>
                            <td><?php echo (int)$row['stok']; ?></td>
                            <td>
                                <a class="link" href="ubah.php?id=<?php echo $row['id_barang']; ?>">Ubah</a>
                                <a class="link del" href="hapus.php?id=<?php echo $row['id_barang']; ?>" onclick="return confirm('Hapus data ini?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">Belum ada data</td>
                    </tr>
                <?php endif; ?>
            </table>
        </div>
    </div>
</body>
</html>
```
### Penjelasan:
- "`include(""koneksi.php"");`", Memanggil file koneksi untuk terhubung ke database.
- `$sql = 'SELECT * FROM data_barang ORDER BY id_barang DESC';` ,Query SQL untuk mengambil semua data dan diurutkan dari ID terbesar (terbaru).
- "`$result = mysqli_query($conn, $sql);`", Menjalankan query ke database.
- `if($result && mysqli_num_rows($result) > 0):`, Memeriksa apakah query berhasil dan ada baris data yang ditemukan.
- `while($row = mysqli_fetch_assoc($result)):`, Perulangan untuk menampilkan setiap baris data.
- `if(!empty($row['gambar']) && file_exists($row['gambar'])):`, Pengecekan apakah data gambar ada dan file gambarnya tersedia di server.
- "`number_format($row['harga_beli'],0,',','.');`", Memformat angka (harga) dengan pemisah ribuan titik.
- "`<a href=""ubah.php?id=..."" >Ubah</a>`", "Tautan ke halaman ubah, mengirimkan id_barang sebagai parameter."
- "`<a href=""hapus.php?id=..."" onclick=""return confirm(...)"">Hapus</a>`", "Tautan ke halaman hapus, disertai konfirmasi JavaScript."

Output:

img

### B. CREATE: Menambah Data (tambah.php)
File ini berisi form input dan logika PHP untuk memproses data serta file upload.
```
<?php 
error_reporting(E_ALL); 
include_once 'koneksi.php'; 
 
if (isset($_POST['submit'])) 
{ 
    $nama = $_POST['nama']; 
    $kategori = $_POST['kategori']; 
    $harga_jual = $_POST['harga_jual']; 
    $harga_beli = $_POST['harga_beli']; 
    $stok = $_POST['stok']; 
    $file_gambar = $_FILES['file_gambar']; 
    $gambar = null; 
    if ($file_gambar['error'] == 0) 
    { 
        $filename = str_replace(' ', '_',$file_gambar['name']); 
        $destination = dirname(__FILE__) .'/gambar/' . $filename; 
        if(move_uploaded_file($file_gambar['tmp_name'], $destination)) 
        { 
            $gambar = 'gambar/' . $filename;; 
        } 
    } 
    $sql = 'INSERT INTO data_barang (nama, kategori,harga_jual, harga_beli, 
stok, gambar) '; 
    $sql .= "VALUE ('{$nama}', '{$kategori}','{$harga_jual}', 
'{$harga_beli}', '{$stok}', '{$gambar}')"; 
    $result = mysqli_query($conn, $sql); 
    header('location: index.php'); 
} 
?> 
<!DOCTYPE html> 
<html lang="en"> 
<head> 
    <meta charset="UTF-8"> 
    <link href="style.css" rel="stylesheet" type="text/css" /> 
    <title>Tambah Barang</title> 
</head> 
<body> 
<div class="container"> 
    <h1>Tambah Barang</h1> 
    <div class="main"> 
        <form method="post" action="tambah.php" 
enctype="multipart/form-data"> 
            <div class="input"> 
                <label>Nama Barang</label> 
                <input type="text" name="nama" /> 
            </div> 
            <div class="input"> 
                <label>Kategori</label> 
                <select name="kategori"> 
                    <option value="Komputer">Komputer</option> 
                    <option value="Elektronik">Elektronik</option> 
                    <option value="Hand Phone">Hand Phone</option> 
                </select> 
            </div> 
            <div class="input"> 
                <label>Harga Jual</label> 
                <input type="text" name="harga_jual" /> 
            </div> 
            <div class="input"> 
                <label>Harga Beli</label> 
                <input type="text" name="harga_beli" /> 
            </div> 
            <div class="input"> 
                <label>Stok</label> 
                <input type="text" name="stok" /> 
            </div> 
            <div class="input"> 
                <label>File Gambar</label> 
                <input type="file" name="file_gambar" /> 
            </div> 
            <div class="submit"> 
                <input type="submit" name="submit" value="Simpan" /> 
            </div> 
        </form> 
    </div> 
</div> 
</body> 
</html> 
```
### Penjelasan:
- `if (isset($_POST['submit']))`, Memeriksa apakah tombol submit (Simpan) telah ditekan.
- `$file_gambar = $_FILES['file_gambar'];`, Mengambil data file yang diunggah.
- `if ($file_gambar['error'] == 0)`, Memeriksa apakah tidak ada error saat upload.
- "`$filename = str_replace(' ', '_',$file_gambar['name']);`", Mengganti spasi pada nama file menjadi underscore.
- `move_uploaded_file(...)`, Fungsi untuk memindahkan file dari lokasi sementara ke folder /gambar/.
- "`$sql = 'INSERT INTO data_barang (nama, kategori,harga_jual, harga_beli, stok, gambar) ...`", Query SQL untuk menyimpan data baru.
- `header('location: index.php');`, Mengarahkan kembali ke halaman index.php setelah penyimpanan berhasil.
- "`<form method=""post"" action=""tambah.php"" enctype=""multipart/form-data"">`", Atribut penting untuk memungkinkan pengiriman data formulir dan file.

Output:
img

### C. UPDATE: Mengubah Data (ubah.php)
File ini berfungsi untuk mengambil data barang berdasarkan ID dan memperbaruinya.
```
<?php
error_reporting(E_ALL);
// Memanggil file koneksi.php
include_once "koneksi.php";

/**
 * Fungsi untuk menentukan opsi 'selected' pada elemen <select>.
 *
 * @param string $var Nilai yang sedang dicek (nilai dari database).
 * @param string $val Nilai opsi saat ini.
 * @return string Mengembalikan 'selected="selected"' jika cocok, atau string kosong.
 */
function is_select($var, $val) {
    return ($var == $val) ? 'selected="selected"' : '';
}

// --- LOGIKA PENGAMBILAN DATA (GET) ---

// Inisialisasi variabel untuk ID
$id = 0;

if (isset($_GET["id"])) {
    // 1. Ambil ID dari parameter URL (GET)
    $id = mysqli_real_escape_string($conn, $_GET["id"]);
} else {
    // 2. Jika ID tidak ada di URL, ambil ID barang pertama
    $sql_first = "SELECT id_barang FROM data_barang ORDER BY id_barang ASC LIMIT 1";
    $result_first = mysqli_query($conn, $sql_first);

    if ($result_first && mysqli_num_rows($result_first) > 0) {
        $row_first = mysqli_fetch_assoc($result_first);
        $id = $row_first["id_barang"];
    } else {
        // Jika tidak ada data sama sekali di tabel
        echo "Tidak ada data barang yang tersedia untuk diubah.";
        exit;
    }
}

// Ambil data barang berdasarkan ID yang sudah ditentukan
$sql = "SELECT * FROM data_barang WHERE id_barang = '{$id}'";
$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) == 0) {
    echo "Data barang dengan ID {$id} tidak ditemukan.";
    exit;
}

// Ambil data sebagai array asosiatif untuk diisi ke form
$data = mysqli_fetch_array($result);

// --- LOGIKA PEMROSESAN FORM (POST) UNTUK UPDATE DATA ---

if (isset($_POST['submit'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
    $harga_jual = (int)$_POST['harga_jual'];
    $harga_beli = (int)$_POST['harga_beli'];
    $stok = (int)$_POST['stok'];
    $gambar_lama = mysqli_real_escape_string($conn, $_POST['gambar_lama']);
    $gambar = $gambar_lama; // Default: gunakan gambar lama

    // Proses upload gambar baru
    if (isset($_FILES['file_gambar']) && $_FILES['file_gambar']['error'] == 0) {
        $file_gambar = $_FILES['file_gambar'];
        $filename = time() . '_' . preg_replace('/\s+/', '_', basename($file_gambar['name']));
        $destination_dir = dirname(__FILE__) . '/gambar/';
        
        // Pastikan direktori ada
        if (!is_dir($destination_dir)) {
            mkdir($destination_dir, 0755, true);
        }
        
        $destination = $destination_dir . $filename;
        
        if (move_uploaded_file($file_gambar['tmp_name'], $destination)) {
            $gambar = 'gambar/' . $filename;
            
            // Hapus gambar lama jika ada dan berhasil upload gambar baru
            if (!empty($gambar_lama) && file_exists($gambar_lama)) {
                unlink($gambar_lama);
            }
        }
    }

    // Query UPDATE data
    $sql_update = "UPDATE data_barang SET 
                nama = '{$nama}', 
                kategori = '{$kategori}', 
                harga_jual = '{$harga_jual}', 
                harga_beli = '{$harga_beli}', 
                stok = '{$stok}', 
                gambar = '{$gambar}' 
                WHERE id_barang = '{$id}'";
    
    $update_success = mysqli_query($conn, $sql_update);
    
    if ($update_success) {
        // Redirect setelah update berhasil
        header('Location: index.php?status=ok');
    } else {
        // Jika update gagal (opsional: tampilkan pesan error)
        header('Location: index.php?status=err');
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Ubah Barang - <?php echo htmlspecialchars($data['nama']); ?></title>
    <link href="style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div class="container">
    <h1>Ubah Barang</h1>
    <div class="main">
        <form method="post" action="ubah.php?id=<?php echo $id; ?>" enctype="multipart/form-data">
            
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <input type="hidden" name="gambar_lama" value="<?php echo htmlspecialchars($data['gambar']); ?>">

            <div class="input">
                <label>Nama Barang</label>
                <input type="text" name="nama" value="<?php echo htmlspecialchars($data['nama']); ?>" required />
            </div>
            
            <div class="input">
                <label>Kategori</label>
                <select name="kategori">
                    <option value="Komputer" <?php echo is_select($data['kategori'], 'Komputer'); ?>>Komputer</option>
                    <option value="Elektronik" <?php echo is_select($data['kategori'], 'Elektronik'); ?>>Elektronik</option>
                    <option value="Hand Phone" <?php echo is_select($data['kategori'], 'Hand Phone'); ?>>Hand Phone</option>
                </select>
            </div>
            
            <div class="input">
                <label>Harga Jual</label>
                <input type="number" name="harga_jual" value="<?php echo htmlspecialchars($data['harga_jual']); ?>" required />
            </div>
            
            <div class="input">
                <label>Harga Beli</label>
                <input type="number" name="harga_beli" value="<?php echo htmlspecialchars($data['harga_beli']); ?>" required />
            </div>
            
            <div class="input">
                <label>Stok</label>
                <input type="number" name="stok" value="<?php echo htmlspecialchars($data['stok']); ?>" required />
            </div>
            
            <div class="input">
                <label>Gambar Saat Ini</label>
                <?php if (!empty($data['gambar'])): ?>
                    <img src="<?php echo htmlspecialchars($data['gambar']); ?>" alt="Gambar Barang" style="max-width: 200px; display: block; margin-bottom: 10px;">
                <?php else: ?>
                    <p>Tidak ada gambar</p>
                <?php endif; ?>
                
                <label>File Gambar (Kosongkan jika tidak ingin mengubah)</label>
                <input type="file" name="file_gambar" accept="image/*" />
            </div>
            
            <div class="submit">
                <input type="submit" name="submit" value="Simpan Perubahan" />
                <a class="btn" href="index.php">Batal</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>
```
### Penjelasan
- "`function is_select($var, $val)`", "Fungsi pembantu untuk mencetak `selected=""selected"` pada `<option>` yang cocok dengan nilai database."
- "`if (isset($_GET[""id""])) { ... } else { ... }`", Logika untuk menentukan ID barang: dari URL (`$_GET`) atau mengambil ID barang pertama jika URL kosong.
- "`mysqli_real_escape_string($conn, $_GET[""id""])`", Membersihkan input ID untuk keamanan (SQL Injection Prevention).
- "`$sql = ""SELECT * FROM data_barang WHERE id_barang = '{$id}'"";`", Mengambil data barang yang akan diubah.
- `if (isset($_POST['submit'])) { ... }`, Logika PHP untuk memproses data yang diubah (mirip `tambah.php`).
- `if (!empty($gambar_lama) && file_exists($gambar_lama)) { unlink($gambar_lama); }`, Menghapus file gambar lama jika user mengupload gambar baru.
- "`$sql_update = ""UPDATE data_barang SET ... WHERE id_barang = '{$id}'"";`", Query SQL untuk memperbarui data berdasarkan ID.
- "`<input type=""hidden"" name=""gambar_lama"" value=""..."">`", Menyimpan path gambar lama agar bisa dihapus jika ada gambar baru.
- "`<input type=""text"" name=""nama"" value=""<?php echo htmlspecialchars($data['nama']); ?>"" ... />`", Mengisi nilai input dengan data yang sudah ada.

Output:
img

### D. DELETE: Menghapus Data (hapus.php)
Skrip sederhana untuk menghapus baris data berdasarkan ID.
```
<?php 
include_once 'koneksi.php'; 
$id = $_GET['id']; 
$sql = "DELETE FROM data_barang WHERE id_barang = '{$id}'"; 
$result = mysqli_query($conn, $sql); 
header('location: index.php'); 
?> 
```

Output:
img

### III. Styling Tampilan (style.css)

File ini menyediakan styling dasar agar tampilan aplikasi lebih rapi dan mudah dibaca.
```
body {
    font-family: Arial;
    background: #ffffff;
    margin: 20px;
}

h1 {
    font-size: 32px;
    font-weight: bold;
    margin-bottom: 20px;
}

a {
    color: #003399;
}

.container {
    width: 100%;
}

table {
    border-collapse: collapse;
    margin-top: 20px;
    width: 100%;
}

table th {
    background: #f2f2f2;
    padding: 12px;
    border: 1px solid #cccccc;
    font-weight: bold;
    font-size: 16px;
    text-align: left;
}

table td {
    padding: 10px;
    border: 1px solid #cccccc;
    font-size: 15px;
}

.thumb {
    width: 120px;
}

.link {
    margin-right: 10px;
}

.link:hover {
    text-decoration: underline;
}

.del {
    color: red;
}

.btn {
    display: inline-block;
    margin-bottom: 15px;
    font-size: 16px;
}


/* Penambahan styling untuk form (tambah.php dan ubah.php) */

.input {
    margin-bottom: 15px;
}

.input label {
    display: block;
    font-weight: bold;
    margin-bottom: 5px;
}

.input input[type="text"],
.input input[type="number"],
.input input[type="file"],
.input select {
    width: 100%;
    padding: 8px;
    border: 1px solid #ccc;
    box-sizing: border-box;
}

.submit input[type="submit"] {
    background-color: #003399;
    color: white;
    padding: 10px 15px;
    border: none;
    cursor: pointer;
    font-size: 16px;
    margin-right: 10px;
}

.submit a.btn {
    text-decoration: none;
    padding: 10px 15px;
    border: 1px solid #ccc;
    color: #333;
    background: #f9f9f9;
}
```
Fokus Styling:
- Tabel: Menggunakan collapse border dan padding yang baik (`table`, `th`, `td`).
- Gambar: Kelas `.thumb` membatasi lebar gambar.
- Aksi: Tautan `.link` dan tautan hapus (`.del`) memiliki warna berbeda.
- Form: Styling untuk input dan label pada halaman `tambah.php` dan `ubah.php`.

### Hasil
Setelah semua file disiapkan dan MySQL Server dijalankan, aplikasi berhasil menjalankan operasi CRUD:

- Read: Daftar barang ditampilkan di index.php.

- Create: Pengguna dapat mengisi form di tambah.php dan data berhasil masuk ke database.
  
- Update: Pengguna dapat mengubah data di ubah.php dan perubahan disimpan.
  
- Delete: Data berhasil dihapus setelah konfirmasi.
