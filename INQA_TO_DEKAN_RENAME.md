# Perubahan InQA menjadi Dekan - Dokumentasi

## Ringkasan

Sistem telah diperbarui untuk mengganti semua referensi "InQA" menjadi "Dekan" di seluruh aplikasi.

## Perubahan yang Dilakukan

### 1. Routes (routes/web.php)

-   ✅ Route prefix: `inqa` → `dekan`
-   ✅ Route names: `inqa.*` → `dekan.*`
-   ✅ Middleware role: `role:Staff InQA` → `role:Dekan`
-   ✅ Namespace controller: `App\Http\Controllers\InQA` → `App\Http\Controllers\Dekan`

### 2. Controllers

-   ✅ Folder direname: `app/Http/Controllers/InQA/` → `app/Http/Controllers/Dekan/`
-   ✅ File direname:
    -   `InqaController.php` → `DekanController.php`
    -   `InqaKpiController.php` → `DekanKpiController.php`
-   ✅ Namespace diupdate: `namespace App\Http\Controllers\InQA;` → `namespace App\Http\Controllers\Dekan;`
-   ✅ Class names:
    -   `InqaController` → `DekanController`
    -   `InqaKpiController` → `DekanKpiController`
-   ✅ View references: `view('inqa.*')` → `view('dekan.*')`

### 3. Views

-   ✅ Folder direname: `resources/views/inqa/` → `resources/views/dekan/`
-   ✅ Layout references:
    -   `@extends('inqa.layouts.main')` → `@extends('dekan.layouts.main')`
    -   `@include('inqa.layouts.*')` → `@include('dekan.layouts.*')`
-   ✅ Route references: `route('inqa.*')` → `route('dekan.*')`
-   ✅ RouteIs checks: `request()->routeIs('inqa.*')` → `request()->routeIs('dekan.*')`
-   ✅ Title: "InQA Dashboard" → "Dekan Dashboard"
-   ✅ Default user: "InQA User" → "Dekan User"

#### View files yang diupdate:

-   `dashboard.blade.php`
-   `layouts/main.blade.php`
-   `layouts/header.blade.php`
-   `layouts/sidebar.blade.php`
-   `kpi/index.blade.php`
-   `kpi/edit.blade.php`
-   `kpi/create.blade.php`
-   `kpi/index_backup.blade.php`
-   `dosen/rekap.blade.php`

### 4. Authentication & Authorization

-   ✅ `AuthController.php`: Role check `'staff inqa'` → `'dekan'`
-   ✅ Login redirect: `route('inqa.dashboard')` → `route('dekan.dashboard')`
-   ✅ Sidebar role checks: `'Staff InQA'` → `'Dekan'`
-   ✅ Admin layout sidebar: `'staff inqa'` → `'dekan'`

### 5. Database Seeder

-   ✅ `UserSeeder.php`:
    -   Username: `'inqa'` → `'dekan'`
    -   Password: `'inqa123'` → `'dekan123'`
    -   Name: `'Staff InQA'` → `'Dekan'`
    -   Role: `'Staff InQA'` → `'Dekan'`

### 6. Cache Clearing

-   ✅ Route cache cleared
-   ✅ View cache cleared
-   ✅ Config cache cleared
-   ✅ Application cache cleared

## Kredensial Login Baru

### Dekan (sebelumnya Staff InQA)

-   **Username**: `dekan`
-   **Password**: `dekan123`
-   **Role**: `Dekan`
-   **URL**: `/dekan/dashboard`

### Kaprodi TI (tidak berubah)

-   **Username**: `kaprodi_ti`
-   **Password**: `kaprodi123`
-   **Role**: `Kaprodi TI`
-   **URL**: `/kaprodi-ti/dashboard`

### Kaprodi SI (tidak berubah)

-   **Username**: `kaprodi_si`
-   **Password**: `kaprodi123`
-   **Role**: `Kaprodi SI`
-   **URL**: `/kaprodi-si/dashboard`

## Langkah Selanjutnya

### 1. Update Database User

Jika sudah ada user dengan role "Staff InQA" di database production, perlu diupdate:

```sql
UPDATE users
SET role = 'Dekan',
    name = 'Dekan',
    username = 'dekan'
WHERE role = 'Staff InQA';
```

### 2. Atau Jalankan Seeder

Jika ingin membuat user baru:

```bash
php artisan db:seed --class=UserSeeder
```

### 3. Testing

Pastikan untuk test:

-   ✅ Login sebagai Dekan
-   ✅ Akses dashboard `/dekan/dashboard`
-   ✅ Filter tahun di dashboard
-   ✅ Akses menu KPI
-   ✅ Akses menu Pengabdian
-   ✅ Akses menu Dosen
-   ✅ Akses menu Mahasiswa

## Catatan Penting

1. **Middleware**: Pastikan middleware `role:Dekan` sudah terdaftar dengan benar
2. **Session**: User yang sedang login sebagai "Staff InQA" perlu logout dan login kembali
3. **Permissions**: Tidak ada perubahan pada permission level, hanya nama role yang berubah
4. **URL**: Semua URL InQA lama (`/inqa/*`) sekarang menjadi (`/dekan/*`)

## Verifikasi

Jalankan command berikut untuk verifikasi:

```bash
# Cek routes
php artisan route:list --name=dekan

# Cek views
ls resources/views/dekan

# Cek controllers
ls app/Http/Controllers/Dekan
```

## Rollback (Jika Diperlukan)

Jika perlu rollback, lakukan langkah berikut:

1. Restore backup database
2. Revert semua file changes menggunakan git
3. Clear cache: `php artisan optimize:clear`

---

**Tanggal Perubahan**: {{ date('Y-m-d') }}
**Dikerjakan oleh**: GitHub Copilot
**Status**: ✅ SELESAI
