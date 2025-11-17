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