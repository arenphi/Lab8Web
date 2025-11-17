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