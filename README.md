# Praktikum 8: PHP dan Database MySQL
Nama : Reynaldi Nugraha Putra <br>
NIM  : 312410278 <br>
Kelas : TI.24.A.3 <br>
Matakuliah : Pemrograman Web Pert 10 <br>

### 1. Membuat Database MySql/MariaDB Menggunakan CMD
- ``cd C:\xampp\mysql\bin``
- ``mysql -u root``
- ``create database latihan1;``
- ``use latihan1;``
- ``CREATE TABLE data_barang ( ``<br>
    ``-> id_barang int(10) auto_increment Primary Key,`` <br>
    ``-> kategori varchar(30),``<br>
    ``-> nama varchar(30),``<br>
    ``-> gambar varchar(100),``<br>
    ``-> harga_beli decimal(10,0),``<br>
    ``-> harga_jual decimal(10,0),``<br>
    ``-> stok int(4)``<br>
    ``-> );``
