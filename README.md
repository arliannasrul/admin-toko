# MitraSpace - Seller Center & Admin Dashboard

MitraSpace Seller Center adalah dashboard administrasi e-commerce premium berbasis web yang dirancang untuk mengelola inventaris barang, order penjualan, pelacakan ekspedisi, hubungan pelanggan (CRM), serta manajemen hak akses pengguna. Sistem ini terintegrasi langsung dengan platform **MitraSpace Buyer Store (Demo)**.

---

## 🚀 Fitur Utama & Modul Sistem

### 1. 📊 Dashboard Utama (Ringkasan Bisnis)
* **Metrik Bisnis**: Menampilkan metrik penting secara real-time seperti total item barang, produk dengan stok kritis, jumlah keluhan pelanggan, dan pesanan yang menunggu proses.
* **Notifikasi Pintar**: Alur notifikasi terpusat untuk aktivitas penting (pesanan masuk baru, keluhan baru, pengajuan admin).

### 2. 📦 Modul Inventaris & Barang (Inventory Management)
* **Manajemen Barang (CRUD)**: Kelola data barang mulai dari foto produk, kategori, harga, hingga stok.
* **Mutasi Stok (Stock Ledger)**: Log riwayat penambahan/pengurangan stok secara detail beserta keterangan alasan (misal: penyesuaian manual, penjualan via toko buyer).
* **Catatan Item**: Kolom feedback/catatan interaktif untuk tiap produk guna koordinasi tim internal toko.

### 3. 🚚 Tracking & Orders (Order Processing)
* **Manajemen Pesanan**: Melacak pesanan masuk, memproses pengiriman (*processing*, *shipping*), hingga pesanan selesai (*completed*).
* **Integrasi Payment Gateway (Doku)**: Sinkronisasi status pembayaran (Paid/Unpaid) secara otomatis berdasarkan callback dari gerbang pembayaran Doku.
* **Integrasi Ongkos Kirim & Kurir (API KiriminAja)**: Memilih opsi ekspedisi pengiriman dengan estimasi biaya ongkir asli secara dinamis.
* **Print Label Pengiriman (Airway Bill)**: Cetak label pengiriman siap pakai secara instan yang dilengkapi dengan barcode dinamis untuk kemudahan proses scan kurir.

### 4. 👥 CRM & Keluhan Pelanggan (Customer Support & CRM)
* **Database Pelanggan**: Menyimpan riwayat nama, nomor HP, total belanjaan (lifetime value), dan histori transaksi masing-masing customer.
* **Tiket Keluhan Terintegrasi**: Mengelola keluhan pelanggan (tiket support) dengan tiga status: *Open*, *In Progress*, dan *Resolved*.
* **Pencatatan Tiket Manual**: Memungkinkan tim sales membuat tiket secara manual berdasarkan chat WA atau telepon dari pelanggan.

### 5. ⚙️ Manajemen Pengguna & Sistem Akses (Role-Based Access Control)
* **4 Pilihan Level Akses (Roles)**:
  * 👑 **Super Admin**: Akses penuh ke seluruh fitur dan manajemen pengguna.
  * 📦 **Staff Gudang (Warehouse)**: Akses khusus ke menu Inventaris, Mutasi Stok, dan cetak Laporan Harian.
  * 🚚 **Staff Penjualan (Sales)**: Akses khusus ke menu Orders, pelacakan kurir, CRM, dan tiket support pelanggan.
  * 👁️ **Guest (Demo Mode)**: Hak akses *read-only* (hanya melihat-lihat) untuk pengguna publik yang ingin mencoba fitur.
* **Pengajuan Akses Admin**: Pengguna baru atau guest dapat memilih dan mengajukan permohonan hak akses khusus (misal: Staff Gudang) ke Super Admin. Super Admin dapat menyetujui/mengubah peran secara instan.
* **Google OAuth**: Login praktis menggunakan Akun Google Anda.

---

## 🎨 Teknologi & Keunggulan UI/UX

* **⚡ SPA-like Transition (Fast Navigation)**: Dilengkapi dengan router AJAX kustom (PJAX) sehingga perpindahan halaman, pemfilteran tabel, dan paginasi berjalan secara instan tanpa perlu memuat ulang seluruh halaman web (*no full page refresh*).
* **📈 Premium Top Progress Bar**: Indikator pemuatan halaman tipis nan elegan di bagian atas layar (seperti GitHub/YouTube) untuk transisi yang memanjakan mata.
* **🌓 Mode Gelap / Terang (Dark & Light Mode)**: Toggle tema modern menggunakan slider switch interaktif dengan transisi ikon ☀️/🌙 yang mulus. Tema dasar secara cerdas mengikuti preferensi pengaturan sistem operasi pengguna.
* **📱 Desain Responsif & Hamburger Menu**: Optimal digunakan baik di desktop maupun perangkat mobile dengan sidebar transisi samping (*drawer overlay*) yang sangat halus.
* **📄 Paginasi Responsif**: Seluruh data tabel menggunakan paginasi responsif yang rapi sehingga konten tidak menumpuk ke bawah pada perangkat dengan layar kecil.

---

## 🛠️ Stack Teknologi

* **Backend**: Laravel (PHP Framework)
* **Frontend**: HTML5, Vanilla JavaScript, CSS3 (Custom Variables)
* **Database**: MySQL / PostgreSQL
* **Autentikasi**: Laravel Auth (Google Socialite / OAuth2)
* **Integrasi API**: Doku Payment Gateway, KiriminAja API
* **Deployment**: Dideploy ke server hosting dengan integrasi otomatis via Vercel (Frontend & Assets) dan database relasional.
