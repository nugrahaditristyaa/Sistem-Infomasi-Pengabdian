# Dashboard Kaprodi - Reuse View InQA dengan Filter Prodi

## Konsep Implementasi

Dashboard Kaprodi **TIDAK** menggunakan view HTML/Blade sendiri. Sebagai gantinya, sistem menggunakan **view yang sama persis** dengan Dashboard InQA (`resources/views/inqa/dashboard.blade.php`), tetapi dengan **data yang sudah difilter otomatis** berdasarkan program studi yang sedang login.

## Perbedaan Kunci

### Dashboard InQA (Dekan)

-   **View**: `resources/views/inqa/dashboard.blade.php`
-   **Data**: Menampilkan **SEMUA** data pengabdian dari kedua prodi (Informatika & Sistem Informasi)
-   **Route**: `/inqa/dashboard`
-   **Controller**: `InQaController::dashboard()`

### Dashboard Kaprodi TI

-   **View**: `resources/views/inqa/dashboard.blade.php` (SAMA dengan InQA)
-   **Data**: Hanya menampilkan data pengabdian yang **melibatkan dosen Informatika**
-   **Route**: `/kaprodi-ti/dashboard`
-   **Controller**: `KaprodiController::dashboardTI()`

### Dashboard Kaprodi SI

-   **View**: `resources/views/inqa/dashboard.blade.php` (SAMA dengan InQA)
-   **Data**: Hanya menampilkan data pengabdian yang **melibatkan dosen Sistem Informasi**
-   **Route**: `/kaprodi-si/dashboard`
-   **Controller**: `KaprodiController::dashboardSI()`

## Implementasi di Controller

### File: `app/Http/Controllers/Kaprodi/KaprodiController.php`

#### Method Utama

```php
public function dashboardTI(Request $request)
{
    $prodi = 'Informatika';
    return $this->dashboard($request, $prodi);
}

public function dashboardSI(Request $request)
{
    $prodi = 'Sistem Informasi';
    return $this->dashboard($request, $prodi);
}

private function dashboard(Request $request, $prodiFilter)
{
    // Semua logika filtering data berdasarkan $prodiFilter
    // ...

    return view('inqa.dashboard', compact(
        'totalKpi',
        'totalMonitoring',
        'avgAchievement',
        'thisMonthMonitoring',
        'recentMonitoring',
        'stats',
        'filterYear',
        'availableYears',
        'kpiRadarData',
        'namaDosen',
        'jumlahPengabdianDosen',
        'jenisLuaranData'
    ));
}
```

## Data yang Difilter

### 1. Statistik Cards

#### Total Pengabdian

```php
// Filter: Pengabdian yang melibatkan dosen dari prodi tertentu
$baseProdiFilter = function($query) use ($prodiFilter) {
    $query->whereExists(function ($subQuery) use ($prodiFilter) {
        $subQuery->select(DB::raw(1))
            ->from('pengabdian_dosen')
            ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
            ->whereColumn('pengabdian_dosen.id_pengabdian', 'pengabdian.id_pengabdian')
            ->where('dosen.prodi', $prodiFilter);
    });
};

$totalPengabdian = Pengabdian::where($baseProdiFilter)->count();
```

#### Dosen Terlibat

```php
// Hanya dosen dari prodi tertentu
$totalDosenTerlibat = DB::table('pengabdian_dosen')
    ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
    ->where('dosen.prodi', $prodiFilter)
    ->distinct('pengabdian_dosen.nik')
    ->count('pengabdian_dosen.nik');
```

#### Mahasiswa Terlibat

```php
// Hanya mahasiswa dari prodi tertentu
$mahasiswa = DB::table('pengabdian_mahasiswa')
    ->join('mahasiswa', 'pengabdian_mahasiswa.nim', '=', 'mahasiswa.nim')
    ->where('mahasiswa.prodi', $prodiFilter)
    ->distinct('pengabdian_mahasiswa.nim')
    ->count('pengabdian_mahasiswa.nim');
```

### 2. Pengabdian Khusus vs Kolaborasi

#### Untuk Kaprodi TI:

```php
// Khusus TI: hanya melibatkan dosen TI
$pengabdianKhususInformatika = DB::table('pengabdian')
    ->whereExists(function ($query) {
        // Ada dosen TI
        $query->select(DB::raw(1))
            ->from('pengabdian_dosen')
            ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
            ->whereColumn('pengabdian_dosen.id_pengabdian', 'pengabdian.id_pengabdian')
            ->where('dosen.prodi', 'Informatika');
    })
    ->whereNotExists(function ($query) {
        // Tidak ada dosen dari prodi lain
        $query->select(DB::raw(1))
            ->from('pengabdian_dosen')
            ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
            ->whereColumn('pengabdian_dosen.id_pengabdian', 'pengabdian.id_pengabdian')
            ->where('dosen.prodi', '!=', 'Informatika');
    })
    ->count();

// Kolaborasi: melibatkan dosen TI DAN dosen SI
$pengabdianKolaborasi = DB::table('pengabdian')
    ->whereExists(function ($query) {
        // Ada dosen TI
    })
    ->whereExists(function ($query) {
        // Ada dosen SI
    })
    ->count();
```

### 3. Chart Rekap Dosen

```php
// Hanya dosen dari prodi tertentu yang memiliki pengabdian
$dosenQuery = Dosen::where('prodi', $prodiFilter)
    ->withCount(['pengabdian as jumlah_pengabdian' => function ($query) use ($filterYear) {
        if ($filterYear !== 'all') {
            $query->whereYear('tanggal_pengabdian', $filterYear);
        }
    }]);

// Filter hanya dosen yang memiliki pengabdian
$dosenQuery->whereHas('pengabdian');

$dosenCounts = $dosenQuery->orderBy('jumlah_pengabdian', 'desc')->get();

$namaDosen = $dosenCounts->pluck('nama');
$jumlahPengabdianDosen = $dosenCounts->pluck('jumlah_pengabdian');
```

### 4. Chart Jenis Luaran

```php
private function getJenisLuaranTreemapDataWithProdiFilter($filterYear, $prodiFilter)
{
    $query = DB::table('luaran')
        ->join('jenis_luaran', 'luaran.id_jenis_luaran', '=', 'jenis_luaran.id_jenis_luaran')
        ->join('pengabdian', 'luaran.id_pengabdian', '=', 'pengabdian.id_pengabdian')
        ->join('pengabdian_dosen', 'pengabdian.id_pengabdian', '=', 'pengabdian_dosen.id_pengabdian')
        ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
        ->where('dosen.prodi', $prodiFilter); // FILTER KUNCI

    if ($filterYear !== 'all') {
        $query->whereYear('pengabdian.tanggal_pengabdian', $filterYear);
    }

    $jenisLuaranData = $query
        ->select(
            'jenis_luaran.nama_jenis_luaran',
            DB::raw('COUNT(DISTINCT luaran.id_luaran) as jumlah')
        )
        ->groupBy('jenis_luaran.id_jenis_luaran', 'jenis_luaran.nama_jenis_luaran')
        ->orderBy('jumlah', 'desc')
        ->get();

    // Transform ke format treemap chart
    // ...
}
```

### 5. Chart Radar KPI

**Catatan**: Untuk saat ini, KPI Radar Chart dikosongkan (`$kpiRadarData = []`) karena KPI biasanya dihitung untuk fakultas secara keseluruhan, bukan per prodi. Jika diperlukan KPI per prodi, implementasi dapat ditambahkan dengan:

```php
private function getKpiRadarDataWithProdiFilter($filterYear, $prodiFilter)
{
    $kpis = Kpi::orderBy('kode')->get();
    $radarData = [];

    foreach ($kpis as $kpi) {
        // Calculate achievement untuk prodi tertentu
        $realisasi = $this->calculateKpiAchievementWithProdiFilter($kpi, $filterYear, $prodiFilter);
        // ... (perhitungan skor normalisasi)
    }

    return $radarData;
}
```

## Filter Tahun

Semua query mendukung filter tahun yang sama seperti dashboard InQA:

```php
if ($filterYear === 'all') {
    // Query semua tahun untuk prodi tertentu
    $totalPengabdian = Pengabdian::where($baseProdiFilter)->count();
} else {
    // Query tahun spesifik untuk prodi tertentu
    $totalPengabdian = Pengabdian::where($baseProdiFilter)
        ->whereYear('tanggal_pengabdian', $filterYear)
        ->count();
}
```

## Comparison dengan Tahun Sebelumnya

Semua perbandingan persentase juga difilter per prodi:

```php
// Tahun sekarang (filtered by prodi)
$totalPengabdianComparison = Pengabdian::where($baseProdiFilter)
    ->whereYear('tanggal_pengabdian', $currentYear)
    ->count();

// Tahun sebelumnya (filtered by prodi)
$totalPengabdianPrevious = Pengabdian::where($baseProdiFilter)
    ->whereYear('tanggal_pengabdian', $currentYear - 1)
    ->count();

// Calculate percentage change
$percentageChangePengabdian = $totalPengabdianPrevious > 0 ?
    round((($totalPengabdianComparison - $totalPengabdianPrevious) / $totalPengabdianPrevious) * 100, 1) :
    ($totalPengabdianComparison > 0 ? 100 : 0);
```

## Keuntungan Pendekatan Ini

### 1. **Konsistensi UI/UX**

-   Semua role (Admin, InQA, Kaprodi TI, Kaprodi SI) melihat interface yang identik
-   User experience konsisten di seluruh sistem
-   Tidak perlu mempelajari layout berbeda

### 2. **Maintainability**

-   Hanya 1 file view yang perlu di-maintain (`inqa/dashboard.blade.php`)
-   Perubahan UI otomatis applied ke semua role
-   Tidak ada duplikasi code HTML/CSS

### 3. **Separation of Concerns**

-   View layer: hanya bertanggung jawab untuk presentasi
-   Controller layer: bertanggung jawab untuk business logic dan filtering data
-   Sesuai prinsip MVC

### 4. **Flexibility**

-   Mudah menambahkan role baru (misal: Kaprodi Prodi Baru)
-   Cukup tambahkan method controller baru dengan filter prodi berbeda
-   View tidak perlu diubah sama sekali

### 5. **Performance**

-   Tidak perlu load multiple views
-   Query sudah optimal dengan proper filtering
-   Cache view hanya perlu 1 file

## Testing

### Test Kaprodi TI

```bash
# Login sebagai kaprodi_ti
Username: kaprodi_ti
Password: kaprodi123

# Expected Results:
- Dashboard menampilkan data pengabdian yang melibatkan dosen Informatika
- Chart rekap dosen hanya menampilkan dosen Informatika
- Jenis luaran hanya dari pengabdian yang melibatkan dosen Informatika
- Statistik kolaborasi menunjukkan pengabdian TI+SI
- Statistik khusus menunjukkan pengabdian yang hanya TI
```

### Test Kaprodi SI

```bash
# Login sebagai kaprodi_si
Username: kaprodi_si
Password: kaprodi_si123

# Expected Results:
- Dashboard menampilkan data pengabdian yang melibatkan dosen Sistem Informasi
- Chart rekap dosen hanya menampilkan dosen Sistem Informasi
- Jenis luaran hanya dari pengabdian yang melibatkan dosen Sistem Informasi
- Statistik kolaborasi menunjukkan pengabdian SI+TI
- Statistik khusus menunjukkan pengabdian yang hanya SI
```

### Comparison Test

```bash
# Bandingkan data:
1. Login sebagai InQA → catat total pengabdian
2. Login sebagai Kaprodi TI → catat total pengabdian TI
3. Login sebagai Kaprodi SI → catat total pengabdian SI

# Verification:
- Total InQA ≥ Total Kaprodi TI
- Total InQA ≥ Total Kaprodi SI
- Pengabdian kolaborasi muncul di kedua dashboard Kaprodi
- Pengabdian khusus hanya muncul di dashboard Kaprodi yang sesuai
```

## Troubleshooting

### Issue: Data tidak muncul di dashboard Kaprodi

**Solution**:

1. Pastikan data dosen memiliki field `prodi` yang benar ('Informatika' atau 'Sistem Informasi')
2. Pastikan pengabdian memiliki relasi ke `pengabdian_dosen` dengan NIK dosen yang valid
3. Check data dengan query:

```sql
SELECT d.prodi, COUNT(*)
FROM pengabdian_dosen pd
JOIN dosen d ON pd.nik = d.nik
GROUP BY d.prodi;
```

### Issue: Dashboard menampilkan data prodi lain

**Solution**:

1. Clear cache: `php artisan cache:clear`
2. Clear route cache: `php artisan route:clear`
3. Verify user role di database (harus exact match: 'Kaprodi TI' atau 'Kaprodi SI')

### Issue: Chart tidak muncul

**Solution**:

1. Check browser console untuk error JavaScript
2. Verify data `$jenisLuaranData` tidak empty di controller
3. Verify `$namaDosen` dan `$jumlahPengabdianDosen` tidak empty

## Future Development

### Potential Enhancements:

1. **KPI per Prodi**: Implementasi perhitungan KPI khusus untuk masing-masing prodi
2. **Export Report**: Tambahkan fitur export PDF/Excel dengan filter prodi
3. **Notification**: Alert untuk Kaprodi ketika ada pengabdian baru dari prodi mereka
4. **Comparison View**: Side-by-side comparison antara TI dan SI
5. **Trend Analysis**: Analisis tren pengabdian per prodi dalam beberapa tahun

## Files Modified

1. **Controller**:

    - `app/Http/Controllers/Kaprodi/KaprodiController.php` - Complete rewrite untuk menggunakan view InQA

2. **Routes** (`routes/web.php`):

    - Added shared API routes accessible by all authenticated admin users:

    ```php
    // Shared API Routes (accessible by InQA, Kaprodi TI, Kaprodi SI)
    Route::middleware(['auth:admin'])->group(function () {
        Route::prefix('inqa/api')->name('inqa.api.')->group(function () {
            Route::get('/sparkline-data', [InQaController::class, 'getSparklineData'])->name('sparkline-data');
            Route::get('/funding-sources', [InQaController::class, 'getFundingSourcesData'])->name('funding-sources');
            Route::get('/statistics-detail', [InQaController::class, 'getStatisticsDetail'])->name('statistics-detail');
        });
    });
    ```

3. **Views** (DELETED):

    - ❌ `resources/views/kaprodi/dashboard-ti.blade.php` - Tidak digunakan lagi
    - ❌ `resources/views/kaprodi/dashboard-si.blade.php` - Tidak digunakan lagi
    - ✅ Menggunakan `resources/views/inqa/dashboard.blade.php`

4. **Layouts** (RETAINED):
    - `resources/views/kaprodi/layouts/` - Masih digunakan untuk halaman pengabdian list
    - Untuk dashboard, layout InQA yang digunakan secara otomatis karena extends di view

## API Routes

Dashboard menggunakan beberapa API endpoint yang sekarang bisa diakses oleh semua role (InQA, Kaprodi TI, Kaprodi SI):

### 1. Sparkline Data API

-   **Route**: `GET /inqa/api/sparkline-data`
-   **Name**: `inqa.api.sparkline-data`
-   **Controller**: `InQaController@getSparklineData`
-   **Purpose**: Menyediakan data trend tahunan untuk sparkline charts (pengabdian, dosen, mahasiswa)
-   **Current Behavior**: Menampilkan data keseluruhan fakultas (tidak difilter per prodi)
-   **Response Format**:

    ```json
    {
        "success": true,
        "pengabdian": [10, 15, 20, 18, 25],
        "dosen": [5, 8, 12, 10, 15],
        "mahasiswa": [50.0, 60.5, 75.0, 80.0, 85.5],
        "years": [2020, 2021, 2022, 2023, 2024],
        "period": "yearly",
        "count": 5
    }
    ```

### 2. Funding Sources API

-   **Route**: `GET /inqa/api/funding-sources?year={year}`
-   **Name**: `inqa.api.funding-sources`
-   **Controller**: `InQaController@getFundingSourcesData`
-   **Purpose**: Menyediakan data sumber dana untuk stacked bar chart
-   **Current Behavior**: Menampilkan data keseluruhan fakultas (tidak difilter per prodi)
-   **Parameters**: `year` (optional) - tahun filter atau 'all'
-   **Response Format**:

    ```json
    {
        "labels": [2023, 2024],
        "datasets": [
            {
                "label": "LPPM",
                "data": [50000000, 60000000],
                "backgroundColor": "#4e73df"
            }
        ],
        "totals": {
            "previous_year": 50000000,
            "current_year": 60000000
        },
        "years": {
            "previous": 2023,
            "current": 2024
        },
        "no_data": false
    }
    ```

### 3. Statistics Detail API

-   **Route**: `GET /inqa/api/statistics-detail?type={type}&year={year}`
-   **Name**: `inqa.api.statistics-detail`
-   **Controller**: `InQaController@getStatisticsDetail`
-   **Purpose**: Menyediakan data detail untuk modal statistics (pengabdian, dosen, mahasiswa, luaran)
-   **Current Behavior**: Menampilkan data keseluruhan fakultas (tidak difilter per prodi)
-   **Parameters**:
    -   `type`: pengabdian | dosen | mahasiswa | luaran
    -   `year`: tahun filter atau 'all'
-   **Response Format** (example for type=pengabdian):

    ```json
    {
        "total": 50,
        "kolaborasi": 15,
        "informatika": 30,
        "sistem_informasi": 20,
        "pengabdian": [
            {
                "id": 1,
                "judul": "Pelatihan Web Development",
                "ketua": "Dr. John Doe",
                "tanggal": "2024-01-15",
                "prodi": "Informatika"
            }
        ]
    }
    ```

### Why Shared API Routes?

API routes ditempatkan di luar middleware role-specific (`role:Staff InQA`, `role:Kaprodi TI`, dll) karena:

1. **View Reuse**: Semua role menggunakan view yang sama (`inqa/dashboard.blade.php`)
2. **Consistency**: Data struktur dan format response harus konsisten
3. **Maintainability**: Tidak perlu duplikasi endpoint untuk setiap role
4. **Security**: Tetap protected dengan `auth:admin` middleware

### Future Enhancement

> **Note**: Untuk sekarang, API endpoints `sparkline-data`, `funding-sources`, dan `statistics-detail` mengembalikan data keseluruhan fakultas (tidak difilter per prodi). Ini membuat Kaprodi bisa melihat trend dan detail keseluruhan fakultas.
>
> Jika diperlukan filter per prodi untuk API endpoints ini, bisa ditambahkan logic untuk detect user role di `InQaController`:
>
> ```php
> $user = Auth::guard('admin')->user();
> if ($user->role === 'Kaprodi TI') {
>     // Apply filter prodi = 'Informatika'
> } elseif ($user->role === 'Kaprodi SI') {
>     // Apply filter prodi = 'Sistem Informasi'
> }
> ```

## Summary

Sistem dashboard Kaprodi berhasil diimplementasikan dengan prinsip **DRY (Don't Repeat Yourself)**:

-   ✅ **1 View** untuk 3 role (InQA, Kaprodi TI, Kaprodi SI)
-   ✅ **Filter di Controller** untuk memisahkan data per prodi
-   ✅ **Konsistensi UI** di seluruh sistem
-   ✅ **Easy Maintenance** karena tidak ada duplikasi code
-   ✅ **Scalable** untuk menambahkan prodi/role baru

Perbedaan HANYA pada **data yang ditampilkan**, bukan pada **tampilan interface**.
