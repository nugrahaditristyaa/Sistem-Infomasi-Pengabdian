# Sistem Informasi Pengabdian Masyarakat FTI UKDW

Sistem informasi untuk mengelola data pengabdian masyarakat, HKI, dan monitoring KPI di Fakultas Teknologi Informasi Universitas Kristen Duta Wacana.

## Fitur Utama

### 1. Manajemen Pengabdian Masyarakat

-   Input data pengabdian masyarakat
-   Manajemen tim dosen dan mahasiswa
-   Tracking mitra dan sumber dana
-   Upload dan manajemen dokumen
-   Monitoring luaran pengabdian

### 2. Manajemen HKI (Hak Kekayaan Intelektual)

-   Pendaftaran HKI dari luaran pengabdian
-   Tracking anggota HKI
-   Monitoring status HKI

### 3. Monitoring KPI

-   Setting target KPI
-   Input pencapaian KPI
-   Dashboard monitoring KPI
-   Laporan pencapaian

### 4. Manajemen Data Master

-   Data dosen
-   Data mahasiswa
-   Kategori SPMI
-   Jenis luaran
-   Jenis dokumen

## Teknologi yang Digunakan

-   **Backend**: Laravel 10.x
-   **Frontend**: Bootstrap 4, jQuery, DataTables
-   **Database**: MySQL
-   **Authentication**: Laravel built-in authentication

## Struktur Database

### Tabel Utama

-   `pengabdian` - Data pengabdian masyarakat
-   `dosen` - Data dosen
-   `mahasiswa` - Data mahasiswa
-   `pengabdian_dosen` - Relasi dosen dengan pengabdian
-   `pengabdian_mahasiswa` - Relasi mahasiswa dengan pengabdian
-   `mitra` - Data mitra pengabdian
-   `sumber_dana` - Sumber dana pengabdian
-   `dokumen` - Dokumen pengabdian
-   `luaran` - Luaran pengabdian
-   `detail_hki` - Detail HKI
-   `anggota_hki` - Anggota HKI
-   `kpi` - Data KPI
-   `monitoring_kpi` - Monitoring pencapaian KPI

### Tabel Master

-   `jenis_luaran` - Jenis luaran
-   `kategori_spmi` - Kategori SPMI
-   `jenis_dokumen` - Jenis dokumen

## Instalasi

### Prerequisites

-   PHP 8.1+
-   Composer
-   MySQL 5.7+
-   Node.js & NPM (untuk asset compilation)

### Langkah Instalasi

1. **Clone repository**

    ```bash
    git clone <repository-url>
    cd skripsii
    ```

2. **Install dependencies**

    ```bash
    composer install
    npm install
    ```

3. **Setup environment**

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

4. **Konfigurasi database di .env**

    ```env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=nama_database
    DB_USERNAME=username
    DB_PASSWORD=password
    ```

5. **Jalankan migration**

    ```bash
    php artisan migrate
    ```

6. **Jalankan seeder**

    ```bash
    php artisan db:seed
    ```

7. **Compile assets**

    ```bash
    npm run dev
    ```

8. **Jalankan server**
    ```bash
    php artisan serve
    ```

## Akses Sistem

-   **URL**: http://localhost:8000
-   **Username**: admin
-   **Password**: admin123

## Struktur Model dan Relasi

### Model Pengabdian

```php
class Pengabdian extends Model
{
    // Relasi dengan dosen (many-to-many)
    public function dosen()

    // Relasi dengan mahasiswa (many-to-many)
    public function mahasiswa()

    // Relasi dengan mitra (one-to-many)
    public function mitra()

    // Relasi dengan sumber dana (one-to-many)
    public function sumberDana()

    // Relasi dengan dokumen (one-to-many)
    public function dokumen()

    // Relasi dengan luaran (one-to-many)
    public function luaran()
}
```

### Model Dosen

```php
class Dosen extends Model
{
    // Relasi dengan pengabdian (many-to-many)
    public function pengabdian()

    // Relasi dengan anggota HKI (one-to-many)
    public function anggotaHki()
}
```

## API Endpoints

### Pengabdian

-   `GET /admin/pengabdian` - List pengabdian
-   `POST /admin/pengabdian` - Create pengabdian
-   `GET /admin/pengabdian/{id}` - Show pengabdian
-   `PUT /admin/pengabdian/{id}` - Update pengabdian
-   `DELETE /admin/pengabdian/{id}` - Delete pengabdian

### Dosen

-   `GET /admin/dosen` - List dosen
-   `POST /admin/dosen` - Create dosen
-   `GET /admin/dosen/{nik}` - Show dosen
-   `PUT /admin/dosen/{nik}` - Update dosen
-   `DELETE /admin/dosen/{nik}` - Delete dosen

### KPI

-   `GET /admin/kpi` - List KPI
-   `GET /admin/kpi-monitoring` - Monitoring KPI
-   `POST /admin/kpi-monitoring` - Store monitoring data

## Dashboard Features

### Statistik Utama

-   Total pengabdian
-   Total dosen
-   Total mahasiswa
-   Total KPI

### Grafik dan Chart

-   Pengabdian per tahun
-   Pencapaian KPI
-   Distribusi luaran

### Recent Activities

-   Pengabdian terbaru
-   Monitoring KPI terbaru

## Contributing

1. Fork the project
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Support

Untuk bantuan dan dukungan, silakan hubungi:

-   Email: support@ukdw.ac.id
-   Phone: +62-24-8316100

## Changelog

### Version 1.0.0

-   Initial release
-   Basic CRUD operations for pengabdian
-   User authentication
-   Dashboard with statistics
-   KPI monitoring system
