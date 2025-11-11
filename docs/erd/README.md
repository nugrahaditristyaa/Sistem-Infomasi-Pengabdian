# ERD untuk modul Pengabdian

File PlantUML: `pengabdian-er.puml`

Ringkasan

-   Diagram ini dibuat berdasarkan method relasi di `app/Models` pada repository.
-   Menampilkan entitas utama: `pengabdian`, `dosen`, `mahasiswa`, `luaran`, `detail_hki`, `dokumen`, `mitra`, `sumber_dana`, `kpi`, dll.

Cara merender (opsi):

1. Menggunakan PlantUML lokal (Java + plantuml.jar)

-   Download plantuml.jar dari https://plantuml.com/download
-   Buka PowerShell di folder `docs/erd` lalu jalankan:

```powershell
# menghasilkan SVG
java -jar path\to\plantuml.jar -tsvg pengabdian-er.puml

# menghasilkan PNG
java -jar path\to\plantuml.jar -tpng pengabdian-er.puml
```

2. Menggunakan extension PlantUML di VS Code

-   Install "PlantUML" extension, buka `pengabdian-er.puml` dan pilih "Preview".

3. Menggunakan PlantUML server (online)

-   Anda dapat menempelkan isi PUML ke layanan PlantUML server untuk merender.

Catatan

-   Diagram menggambarkan struktur relasi yang diekstrak dari model Eloquent; jika ada perubahan skema di migration atau model, perbarui file `pengabdian-er.puml`.
-   Bila ingin, saya bisa juga merender PNG/SVG dan menambahkannya ke folder `docs/erd/` — beri tahu apakah Anda ingin saya buat file gambar otomatis (perlu akses ke plantuml server atau menjalankan plantuml jar di env saya; saya bisa menambahkan file .puml dan instruksi, rendering lokal tetap di mesin Anda).
