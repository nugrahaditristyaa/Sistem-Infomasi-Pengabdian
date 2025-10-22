# Dokumentasi Akses Kaprodi TI dan Kaprodi SI

## Overview

Sistem sekarang memiliki akses untuk **Kaprodi Teknik Informatika (TI)** dan **Kaprodi Sistem Informasi (SI)**. Setiap Kaprodi dapat melihat dashboard dan data pengabdian yang spesifik untuk prodi mereka.

## User Credentials

### Kaprodi Teknik Informatika

-   **Username**: `kaprodi_ti`
-   **Password**: `kaprodi123`
-   **Role**: `Kaprodi TI`
-   **Akses**: Dashboard dan data pengabdian untuk prodi Informatika

### Kaprodi Sistem Informasi

-   **Username**: `kaprodi_si`
-   **Password**: `kaprodi123`
-   **Role**: `Kaprodi SI`
-   **Akses**: Dashboard dan data pengabdian untuk prodi Sistem Informasi

## Fitur Dashboard Kaprodi

### 1. Statistik Cards

Dashboard menampilkan 4 kartu statistik utama:

-   **Total Pengabdian**: Jumlah total pengabdian yang melibatkan dosen dari prodi
-   **Pengabdian Tahun Ini**: Jumlah pengabdian di tahun berjalan
-   **Dosen Terlibat**: Jumlah dosen yang terlibat dalam pengabdian (dari total dosen prodi)
-   **Total Dana**: Total dana pengabdian dari prodi

Setiap kartu dilengkapi dengan tooltip informatif.

### 2. Charts

#### Tren Pengabdian (Line Chart)

-   Menampilkan tren pengabdian dalam 5 tahun terakhir
-   Grafik garis dengan area fill
-   Tooltip menampilkan jumlah pengabdian per tahun

#### Top 10 Dosen Teraktif (Horizontal Bar Chart)

-   Menampilkan 10 dosen dengan pengabdian terbanyak
-   Nama dosen ditruncate jika terlalu panjang
-   Tooltip menampilkan nama lengkap dan jumlah pengabdian

### 3. Tabel Pengabdian Terbaru

-   Menampilkan 5 pengabdian terbaru dari prodi
-   Kolom: Judul, Ketua, Tanggal, Mitra
-   Tombol "Lihat Semua Pengabdian" untuk melihat daftar lengkap

## Halaman Daftar Pengabdian

### Fitur:

1. **Filter Tahun**: Dropdown untuk filter pengabdian berdasarkan tahun
2. **DataTables**: Tabel interaktif dengan fitur search dan sorting
3. **Pagination**: Pagination Laravel untuk navigasi data
4. **Data yang ditampilkan**:
    - Nomor urut
    - Judul pengabdian
    - Ketua pengabdian
    - Tanggal
    - Mitra
    - Sumber dana

## Struktur Route

### Kaprodi TI Routes (Prefix: `/kaprodi-ti`)

-   `GET /kaprodi-ti/dashboard` - Dashboard Kaprodi TI
-   `GET /kaprodi-ti/pengabdian` - Daftar pengabdian Informatika

### Kaprodi SI Routes (Prefix: `/kaprodi-si`)

-   `GET /kaprodi-si/dashboard` - Dashboard Kaprodi SI
-   `GET /kaprodi-si/pengabdian` - Daftar pengabdian Sistem Informasi

### Middleware

Semua route Kaprodi dilindungi dengan middleware:

```php
['auth:admin', 'role:Kaprodi TI']  // untuk TI
['auth:admin', 'role:Kaprodi SI']  // untuk SI
```

## Struktur File

### Controller

```
app/Http/Controllers/Kaprodi/KaprodiController.php
```

Berisi method:

-   `dashboardTI()` - Dashboard untuk Kaprodi TI
-   `dashboardSI()` - Dashboard untuk Kaprodi SI
-   `pengabdianList()` - Daftar pengabdian dengan filter
-   `getStatsByProdi()` - Private method untuk mengambil statistik per prodi

### Views

#### Layouts

```
resources/views/kaprodi/layouts/
├── main.blade.php      # Layout utama
├── sidebar.blade.php   # Sidebar menu
├── header.blade.php    # Top navbar
└── footer.blade.php    # Footer
```

#### Pages

```
resources/views/kaprodi/
├── dashboard-ti.blade.php  # Dashboard Kaprodi TI
├── dashboard-si.blade.php  # Dashboard Kaprodi SI
└── pengabdian.blade.php    # Daftar pengabdian
```

## Database Query Logic

### Filter Data per Prodi

Data pengabdian difilter berdasarkan prodi dosen yang terlibat:

```php
// Kaprodi TI: prodi = 'Informatika'
// Kaprodi SI: prodi = 'Sistem Informasi'
```

### Queries yang Digunakan:

1. **Total Pengabdian**: JOIN pengabdian → pengabdian_dosen → dosen (filtered by prodi)
2. **Dosen Terlibat**: COUNT DISTINCT dari pengabdian_dosen yang dosen.prodi = X
3. **Total Dana**: SUM dari sumber_dana yang pengabdian melibatkan dosen prodi X
4. **Top Dosen**: GROUP BY dosen dengan ORDER BY COUNT pengabdian
5. **Tren per Tahun**: GROUP BY YEAR dengan COUNT pengabdian

## Design & UI

### Color Scheme

-   **Sidebar**: Gradient Info (biru tosca)
-   **Cards**: Border-left dengan warna sesuai kategori
    -   Primary (biru): Total pengabdian
    -   Success (hijau): Pengabdian tahun ini
    -   Info (biru muda): Dosen terlibat
    -   Warning (kuning): Total dana

### Modern UI Elements

-   Cards dengan border-radius 16px
-   Shadow effects dengan hover animation
-   Chart.js v3 dengan tooltips custom
-   Bootstrap 4 responsive grid
-   Font: Nunito (Google Fonts)

## Login Flow

### AuthController Update

Login redirect berdasarkan role:

```php
if ($user->role === 'Kaprodi TI') {
    return redirect()->route('kaprodi.ti.dashboard');
} elseif ($user->role === 'Kaprodi SI') {
    return redirect()->route('kaprodi.si.dashboard');
}
```

### User Creation

Users dibuat melalui:

1. **Seeder**: `database/seeders/UserSeeder.php`

    ```bash
    php artisan db:seed --class=UserSeeder
    ```

2. **Manual via Tinker**:
    ```bash
    php artisan tinker
    User::create([
        'username' => 'kaprodi_ti',
        'password' => Hash::make('kaprodi123'),
        'name' => 'Kaprodi TI',
        'role' => 'Kaprodi TI'
    ])
    ```

## Testing

### Login Test

1. Akses: `http://localhost:8000/login`
2. Login sebagai Kaprodi TI:
    - Username: `kaprodi_ti`
    - Password: `kaprodi123`
3. Verify redirect ke: `/kaprodi-ti/dashboard`
4. Verify data yang ditampilkan adalah data Informatika

### Access Control Test

1. Login sebagai Kaprodi TI
2. Coba akses: `/kaprodi-si/dashboard`
3. Expected: 403 Unauthorized (ditolak oleh role middleware)

## Troubleshooting

### Issue: 404 Not Found

**Solution**: Clear route cache

```bash
php artisan route:clear
php artisan route:cache
```

### Issue: 500 Server Error

**Solution**:

1. Check logs: `storage/logs/laravel.log`
2. Verify database connection
3. Run migrations jika belum: `php artisan migrate`

### Issue: Data tidak muncul

**Solution**:

1. Verify data dosen memiliki field `prodi` dengan value 'Informatika' atau 'Sistem Informasi'
2. Verify pengabdian_dosen table memiliki relasi yang benar
3. Check query di controller method `getStatsByProdi()`

## Future Enhancements

Possible features untuk pengembangan selanjutnya:

1. Export data pengabdian ke Excel/PDF
2. Filter tambahan (berdasarkan mitra, sumber dana, dll)
3. Detail view untuk setiap pengabdian
4. Perbandingan statistik antar tahun
5. Notifikasi untuk pengabdian baru
6. Dashboard analytics yang lebih mendalam
