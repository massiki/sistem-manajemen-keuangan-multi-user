# BRIEF PROJECT — Sistem Manajemen Keuangan Multi-User

## 1. Gambaran Umum

### Nama Sistem
**Sistem Manajemen Keuangan / Digital Buku Kas**

### Konsep
Sistem berbasis web untuk menggantikan proses pencatatan uang masuk dan uang keluar yang saat ini masih dilakukan secara manual menggunakan buku dan pensil.

Sistem dirancang sebagai **multi-user**, sehingga setiap pengguna memiliki data keuangan masing-masing dan tidak dapat melihat atau mengubah data pengguna lain.

Sistem berfokus pada pencatatan kas yang sederhana, cepat, dan mudah digunakan oleh pengguna non-akuntansi.

---

## 2. Latar Belakang

Proses pencatatan keuangan manual memiliki beberapa kendala:

- Pencatatan membutuhkan waktu.
- Perhitungan saldo dilakukan secara manual.
- Risiko salah hitung cukup tinggi.
- Data sulit dicari ketika membutuhkan transaksi tertentu.
- Sulit mengetahui total pemasukan dan pengeluaran dalam periode tertentu.
- Sulit membuat laporan keuangan.
- Data dalam buku berisiko rusak atau hilang.
- Analisis pengeluaran berdasarkan kategori sulit dilakukan.

Sistem digital dibuat untuk menyelesaikan masalah tersebut dengan menyediakan pencatatan transaksi, perhitungan saldo otomatis, dashboard, laporan, serta export data.

---

## 3. Tujuan Sistem

### Tujuan Utama

Membangun sistem yang mempermudah pengguna dalam:

1. Mencatat uang masuk.
2. Mencatat uang keluar.
3. Mengelola kategori transaksi.
4. Mengelola satu atau beberapa sumber kas.
5. Menghitung saldo secara otomatis.
6. Melihat riwayat transaksi.
7. Memantau kondisi keuangan melalui dashboard.
8. Membuat laporan berdasarkan periode.
9. Mengekspor laporan ke PDF dan Excel.
10. Mengelola data keuangan secara aman berdasarkan akun pengguna.

### Prinsip Utama

> Satu transaksi harus dapat dicatat dengan cepat tanpa membutuhkan pemahaman akuntansi yang kompleks.

---

# 4. Target Pengguna

Sistem ditujukan untuk:

- Individu.
- Keluarga.
- Pengelola rumah tangga.
- Pemilik usaha kecil.
- Pengelola kas sederhana.
- Pengguna yang sebelumnya melakukan pencatatan menggunakan buku.

Sistem tidak ditujukan sebagai software akuntansi perusahaan yang kompleks.

---

# 5. Konsep Multi-User

Sistem harus mendukung banyak pengguna.

Contoh:

```text
User A
├── Accounts
├── Categories
├── Transactions
└── Reports

User B
├── Accounts
├── Categories
├── Transactions
└── Reports
```

Data User A tidak boleh dapat diakses oleh User B.

### Data Ownership

Setiap data yang bersifat milik pengguna harus mempunyai relasi terhadap user:

- Account
- Category
- Transaction
- Data konfigurasi pengguna lainnya

Setiap proses pengambilan, perubahan, dan penghapusan data harus memastikan data tersebut merupakan milik user yang sedang login.

---

# 6. Modul Sistem

## 6.1 Authentication

Fitur:

- Register
- Login
- Logout
- Forgot password
- Reset password
- Profile pengguna

### Data User

- Nama
- Email
- Password
- Foto profil (opsional)

---

# 7. Dashboard

Dashboard merupakan halaman utama setelah pengguna login.

### Informasi yang ditampilkan

#### Ringkasan

- Saldo saat ini
- Total uang masuk
- Total uang keluar
- Jumlah transaksi

#### Grafik

- Grafik pemasukan dan pengeluaran berdasarkan waktu.
- Grafik pengeluaran berdasarkan kategori.

#### Transaksi Terakhir

Menampilkan beberapa transaksi terbaru.

Contoh:

```text
Saldo Saat Ini
Rp1.710.000

Uang Masuk
Rp5.742.000

Uang Keluar
Rp3.778.000
```

Dashboard harus menggunakan data milik user yang sedang login.

---

# 8. Account / Kas

Account digunakan untuk menyimpan sumber dana yang dimiliki user.

Contoh:

```text
Kas Rumah
Kas Usaha
Bank
Dompet
```

### Fitur

- Tambah account
- Edit account
- Hapus account
- Lihat saldo account
- Atur saldo awal
- Lihat transaksi berdasarkan account

### Field

```text
id
user_id
name
type
initial_balance
description
created_at
updated_at
```

### Catatan

Untuk penggunaan sederhana, user dapat menggunakan satu account saja.

Untuk penggunaan lebih lanjut, user dapat memiliki beberapa account.

---

# 9. Kategori

Kategori digunakan untuk mengelompokkan transaksi.

Kategori mempunyai dua jenis:

```text
income
expense
```

## 9.1 Kategori Pemasukan

Contoh:

- Gaji
- Honor
- Pendapatan
- Bonus
- Pengembalian Uang
- Pendapatan Lain
- Lainnya

## 9.2 Kategori Pengeluaran

Contoh:

- Makanan / Dapur
- Transportasi
- Listrik
- Internet / WiFi
- ATK
- Belanja Rumah Tangga
- Servis
- Pengiriman
- Honor
- Konsumsi
- Lainnya

### Fitur

- Melihat kategori
- Menambah kategori
- Mengubah kategori
- Menghapus kategori

Kategori custom harus menjadi milik user.

---

# 10. Transaksi

Transaksi merupakan modul utama sistem.

User dapat mencatat dua jenis transaksi:

```text
Uang Masuk
Uang Keluar
```

## 10.1 Form Tambah Transaksi

Field:

- Tanggal transaksi
- Account / sumber dana
- Jenis transaksi
- Kategori
- Nominal
- Keterangan
- Catatan

Contoh:

```text
Jenis:
Uang Keluar

Tanggal:
09 Agustus 2026

Account:
Kas Rumah

Kategori:
Belanja Rumah Tangga

Nominal:
Rp150.000

Keterangan:
Membeli kebutuhan dapur

Catatan:
-
```

---

# 11. Perhitungan Saldo

Saldo harus dihitung otomatis oleh sistem.

Rumus:

```text
Saldo Akhir =
Saldo Awal
+ Total Uang Masuk
- Total Uang Keluar
```

Contoh:

```text
Saldo Awal       Rp2.000.000
Uang Masuk       Rp1.000.000
Uang Keluar      Rp350.000
--------------------------------
Saldo Akhir      Rp2.650.000
```

User tidak perlu memasukkan saldo setelah setiap transaksi.

Sistem harus menghitung saldo berdasarkan transaksi yang tersimpan.

---

# 12. Riwayat Transaksi

Halaman ini merupakan versi digital dari buku kas manual.

### Kolom

| Tanggal | Keterangan | Kategori | Masuk | Keluar | Saldo |
|---|---|---|---:|---:|---:|

### Fitur

- Menampilkan semua transaksi.
- Search transaksi.
- Filter berdasarkan tanggal.
- Filter berdasarkan account.
- Filter berdasarkan kategori.
- Filter uang masuk / uang keluar.
- Sort transaksi.
- Pagination.
- Detail transaksi.
- Edit transaksi.
- Hapus transaksi.

### Contoh

```text
09/08/2026
Membeli kebutuhan dapur
Belanja Rumah Tangga
-Rp150.000
```

---

# 13. Detail Transaksi

Saat user membuka transaksi, sistem menampilkan:

- Tanggal
- Jenis transaksi
- Account
- Kategori
- Nominal
- Keterangan
- Catatan
- Waktu dibuat
- Waktu terakhir diperbarui

User dapat:

- Edit
- Hapus

---

# 14. Laporan

Modul laporan digunakan untuk melihat kondisi keuangan berdasarkan periode.

### Filter

- Hari ini
- Minggu ini
- Bulan ini
- Tahun ini
- Custom periode

### Ringkasan Laporan

```text
Saldo Awal
Total Pemasukan
Total Pengeluaran
Saldo Akhir
```

### Analisis

- Pemasukan berdasarkan kategori.
- Pengeluaran berdasarkan kategori.
- Pemasukan berdasarkan periode.
- Pengeluaran berdasarkan periode.

---

# 15. Export Laporan

Sistem menyediakan export:

- PDF
- Excel

### Laporan PDF

Minimal berisi:

```text
LAPORAN KEUANGAN

Nama Pengguna
Periode

Saldo Awal
Total Pemasukan
Total Pengeluaran
Saldo Akhir

Daftar Transaksi
```

### Export Excel

Data transaksi dapat diekspor dengan kolom:

```text
Tanggal
Account
Jenis
Kategori
Keterangan
Nominal
Catatan
```

---

# 16. Transaksi Rutin

Fitur lanjutan untuk transaksi yang berulang.

Contoh:

- WiFi bulanan.
- Listrik bulanan.
- Honor bulanan.
- Kas bulanan.
- Biaya langganan.

### Field

```text
Nama
Account
Kategori
Jenis
Nominal
Frekuensi
Tanggal
Status Aktif
```

Contoh:

```text
Nama:
WiFi

Nominal:
Rp203.000

Frekuensi:
Bulanan

Tanggal:
10

Status:
Aktif
```

Fitur ini dapat dikembangkan setelah MVP selesai.

---

# 17. Pengaturan

Menu pengaturan meliputi:

### Profil

- Nama
- Email
- Foto profil
- Password

### Pengaturan Keuangan

- Mata uang
- Format angka
- Saldo awal
- Pengaturan account

### Pengaturan Sistem

- Preferensi tampilan
- Notifikasi (opsional)

---

# 18. Struktur Database

Struktur database utama yang direkomendasikan:

```text
users
  │
  ├── accounts
  │       │
  │       └── transactions
  │
  ├── categories
  │       │
  │       └── transactions
  │
  └── transactions
```

## 18.1 Users

```text
id
name
email
password
created_at
updated_at
```

## 18.2 Accounts

```text
id
user_id
name
type
initial_balance
description
created_at
updated_at
```

## 18.3 Categories

```text
id
user_id
name
type
created_at
updated_at
```

## 18.4 Transactions

```text
id
user_id
account_id
category_id
transaction_date
type
amount
description
notes
created_at
updated_at
```

---

# 19. Relasi Database

### User

```text
User hasMany Accounts
User hasMany Categories
User hasMany Transactions
```

### Account

```text
Account belongsTo User
Account hasMany Transactions
```

### Category

```text
Category belongsTo User
Category hasMany Transactions
```

### Transaction

```text
Transaction belongsTo User
Transaction belongsTo Account
Transaction belongsTo Category
```

---

# 20. Aturan Keamanan Data

Ini merupakan bagian penting karena sistem bersifat multi-user.

### Rule 1 — Data Isolation

User hanya dapat melihat data miliknya sendiri.

### Rule 2 — Authorization

User hanya dapat:

- Melihat
- Membuat
- Mengubah
- Menghapus

data yang dimilikinya.

### Rule 3 — Foreign Key

Relasi data harus menggunakan foreign key.

### Rule 4 — Server-side Validation

Validasi tidak hanya dilakukan pada frontend.

### Rule 5 — Authorization pada setiap request

Jangan hanya mengandalkan ID dari URL.

Contoh:

```php
Transaction::where('user_id', auth()->id())
    ->findOrFail($id);
```

Bukan hanya:

```php
Transaction::findOrFail($id);
```

### Rule 6 — Data user lain tidak boleh bocor

Semua query dashboard, laporan, transaksi, kategori, dan account harus dibatasi berdasarkan user yang sedang login.

---

# 21. Alur Utama Sistem

## 21.1 User Baru

```text
Register
   ↓
Login
   ↓
Setup Account
   ↓
Input Saldo Awal
   ↓
Default Categories
   ↓
Dashboard
```

## 21.2 Menambah Uang Masuk

```text
Dashboard
   ↓
Tambah Transaksi
   ↓
Pilih Uang Masuk
   ↓
Pilih Kategori
   ↓
Input Nominal
   ↓
Simpan
   ↓
Saldo Bertambah
```

## 21.3 Menambah Uang Keluar

```text
Dashboard
   ↓
Tambah Transaksi
   ↓
Pilih Uang Keluar
   ↓
Pilih Kategori
   ↓
Input Nominal
   ↓
Simpan
   ↓
Saldo Berkurang
```

## 21.4 Melihat Laporan

```text
Dashboard
   ↓
Laporan
   ↓
Pilih Periode
   ↓
Sistem Mengambil Transaksi User
   ↓
Hitung Ringkasan
   ↓
Tampilkan Grafik
   ↓
Export PDF / Excel
```

---

# 22. MVP — Versi Pertama

Prioritas pengembangan:

### Priority 1

- Authentication
- Multi-user
- Data isolation
- Account / Kas
- Kategori
- Tambah transaksi
- Uang masuk
- Uang keluar
- Perhitungan saldo
- Riwayat transaksi

### Priority 2

- Dashboard
- Search
- Filter
- Edit transaksi
- Hapus transaksi
- Laporan bulanan

### Priority 3

- Grafik
- Export PDF
- Export Excel

### Priority 4

- Transaksi rutin
- Multiple account yang lebih kompleks
- Notifikasi
- Backup
- Fitur tambahan lainnya

---

# 23. Halaman Sistem

Struktur halaman:

```text
PUBLIC
├── Landing Page
├── Login
├── Register
├── Forgot Password
└── Reset Password

AUTHENTICATED
├── Dashboard
├── Transactions
│   ├── All Transactions
│   ├── Income
│   ├── Expense
│   ├── Create
│   ├── Detail
│   └── Edit
├── Accounts
├── Categories
├── Reports
├── Recurring Transactions
└── Settings
    ├── Profile
    ├── Finance Settings
    └── System Settings
```

---

# 24. Prinsip UI/UX

Karena target utama adalah pengguna yang terbiasa menggunakan buku kas manual, UI harus:

- Sederhana.
- Bersih.
- Mudah dipahami.
- Tidak terlalu banyak menu.
- Menggunakan istilah "Uang Masuk" dan "Uang Keluar".
- Form transaksi tidak terlalu panjang.
- Tombol tambah transaksi mudah ditemukan.
- Nominal menggunakan format Rupiah.
- Warna dapat membantu membedakan pemasukan dan pengeluaran.
- Responsive untuk desktop, tablet, dan mobile.

### Tombol utama Dashboard

```text
[ + Uang Masuk ]

[ - Uang Keluar ]

[ Riwayat Transaksi ]
```

---

# 25. Teknologi yang Direkomendasikan

Jika sistem dibangun menggunakan Laravel:

### Backend

- Laravel
- PHP
- MySQL / MariaDB

### Authentication

- Laravel Breeze / Laravel Fortify atau solusi authentication Laravel yang sesuai.

### Frontend

Dapat menggunakan:

- Blade
- Tailwind CSS
- Alpine.js

atau stack frontend lain sesuai kebutuhan project.

### Export

- PDF menggunakan DomPDF.
- Excel menggunakan Laravel Excel / PhpSpreadsheet.

### Chart

Dapat menggunakan:

- Chart.js
- Library chart lain yang kompatibel.

---

# 26. Non-Functional Requirements

## Performance

- Dashboard harus dapat dimuat dengan cepat.
- Query harus dibatasi berdasarkan user.
- Gunakan pagination untuk transaksi dalam jumlah besar.

## Security

- Password di-hash.
- CSRF protection.
- Authorization.
- Validation.
- SQL injection protection melalui Eloquent/Query Builder.
- Data isolation antar user.

## Responsive

Sistem harus dapat digunakan pada:

- Desktop
- Laptop
- Tablet
- Smartphone

## Usability

Pengguna baru harus dapat memahami cara mencatat transaksi tanpa membutuhkan tutorial panjang.

---

# 27. Contoh Skenario

## Skenario 1 — User mencatat pengeluaran

User memiliki saldo:

```text
Rp2.000.000
```

Kemudian membeli kebutuhan dapur:

```text
Rp150.000
```

Sistem menyimpan:

```text
Type:
expense

Category:
Belanja Rumah Tangga

Amount:
150000
```

Saldo menjadi:

```text
Rp1.850.000
```

---

## Skenario 2 — User menerima uang

Saldo:

```text
Rp1.850.000
```

Menerima honor:

```text
Rp500.000
```

Saldo otomatis:

```text
Rp2.350.000
```

---

## Skenario 3 — User mencoba mengakses transaksi user lain

```text
User A
    ↓
Request Transaction milik User B
    ↓
Authorization
    ↓
Access Denied / 404
```

Data User B tidak boleh ditampilkan.

---

# 28. Pengembangan Bertahap

## Phase 1 — Core Cash Management

- Authentication
- Multi-user
- Account
- Category
- Transaction
- Automatic balance
- Transaction history

## Phase 2 — Reporting

- Dashboard
- Monthly report
- Category analysis
- Charts
- Search & filter

## Phase 3 — Document

- PDF export
- Excel export

## Phase 4 — Automation

- Recurring transactions
- Notifications
- Reminder

## Phase 5 — Advanced

- Multiple accounts
- Backup
- Audit log
- Advanced financial analytics
- Optional SaaS subscription

---

# 29. Kesimpulan

Sistem yang dibangun bukan sekadar aplikasi untuk mencatat pemasukan dan pengeluaran, tetapi merupakan **digitalisasi buku kas manual** dengan kemampuan:

```text
Buku Kas Manual
      ↓
Digitalisasi
      ↓
Multi-User
      ↓
Pencatatan Otomatis
      ↓
Saldo Otomatis
      ↓
Dashboard
      ↓
Laporan
      ↓
PDF / Excel
```

Fokus utama sistem adalah **kesederhanaan, kecepatan pencatatan, keamanan data antar-user, dan kemudahan melihat kondisi keuangan**.

> Prioritas utama: pengguna dapat mencatat uang masuk atau uang keluar dengan cepat, kemudian sistem secara otomatis memperbarui saldo dan menyajikan data tersebut dalam bentuk riwayat, dashboard, dan laporan.
