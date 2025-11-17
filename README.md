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

A. Membuat struktur tabel untuk menyimpan data barang.
- ``CREATE TABLE data_barang ( ``<br>
    ``-> id_barang int(10) auto_increment Primary Key,`` <br>
    ``-> kategori varchar(30),``<br>
    ``-> nama varchar(30),``<br>
    ``-> gambar varchar(100),``<br>
    ``-> harga_beli decimal(10,0),``<br>
    ``-> harga_jual decimal(10,0),``<br>
    ``-> stok int(4)``<br>
    ``-> );``

B. Memasukkan beberapa data awal untuk menguji fungsi Read dan Update.
-  ``INSERT INTO data_barang (kategori, nama, gambar, harga_beli, harga_jual, stok)`` <br>
    ``-> VALUES ('Elektronik', 'HP Samsung Android', 'hp_samsung.jpg', 2000000, 2400000, 5),``<br>
    ``-> ('Elektronik', 'HP Xiaomi Android', 'hp_xiaomi.jpg', 1000000, 1400000, 5),``<br>
    ``-> ('Elektronik', 'HP OPPO Android', 'hp_oppo.jpg', 1800000, 2300000, 5);``<br>

``CREATE TABLE data_barang ( 
    id_barang int(10) auto_increment Primary Key, 
    kategori varchar(30), 
    nama varchar(30), 
    gambar varchar(100), 
    harga_beli decimal(10,0), 
    harga_jual decimal(10,0), 
    stok int(4) 
   ); ``
