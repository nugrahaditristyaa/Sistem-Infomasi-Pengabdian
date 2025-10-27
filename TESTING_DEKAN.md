# Dokumen Pengujian Usability - Role Dekan

## Sistem Informasi Pengabdian Masyarakat

---

## 1. INFORMASI UMUM

### Profil Responden

-   **Role**: Dekan
-   **Target Pengguna**: Pimpinan Fakultas yang mengawasi kegiatan pengabdian masyarakat
-   **Pengalaman**: Memiliki pengalaman dalam monitoring dan evaluasi kegiatan akademik

### Akses Login

-   **Username**: dekan
-   **Password**: [Sesuai kredensial sistem]
-   **URL**: [URL Aplikasi]

---

## 2. TASK SCENARIOS - USABILITY TESTING

### Task 1: Login ke Sistem

**Tujuan**: Menguji kemudahan akses masuk ke sistem

**Skenario**:

> "Anda adalah Dekan Fakultas yang ingin melihat laporan kegiatan pengabdian masyarakat. Silakan login ke sistem menggunakan kredensial yang telah diberikan."

**Langkah yang Diharapkan**:

1. Buka halaman login
2. Masukkan username "dekan"
3. Masukkan password
4. Klik tombol login
5. Berhasil masuk ke dashboard

**Kriteria Sukses**:

-   [ ] Berhasil login dalam waktu < 1 menit
-   [ ] Tidak ada error message
-   [ ] Langsung masuk ke dashboard dekan

**SEQ (Single Ease Question)**: Seberapa mudah menyelesaikan task ini? (Skala 1-7)

---

### Task 2: Melihat Statistik Overview di Dashboard

**Tujuan**: Menguji pemahaman informasi statistik umum

**Skenario**:

> "Anda ingin mengetahui berapa total pengabdian yang telah dilakukan, jumlah dosen yang terlibat, dan berapa pengabdian yang melibatkan mahasiswa. Temukan informasi tersebut di dashboard."

**Langkah yang Diharapkan**:

1. Perhatikan bagian kartu statistik di dashboard
2. Identifikasi "Total Pengabdian"
3. Identifikasi "Dosen Terlibat"
4. Identifikasi "Dengan Mahasiswa"
5. Perhatikan sparkline chart pada setiap kartu

**Kriteria Sukses**:

-   [ ] Dapat menemukan semua statistik dalam < 30 detik
-   [ ] Memahami arti setiap statistik
-   [ ] Dapat melihat trend dari sparkline

**SEQ**: Seberapa mudah menyelesaikan task ini? (Skala 1-7)

---

### Task 3: Melihat Detail Pengabdian dari Statistik

**Tujuan**: Menguji kemudahan akses detail data

**Skenario**:

> "Anda ingin mengetahui daftar lengkap pengabdian beserta judulnya, siapa ketuanya, dan mahasiswa yang terlibat. Klik tombol 'Lihat Detail' pada kartu 'Total Pengabdian'."

**Langkah yang Diharapkan**:

1. Perhatikan tombol "Lihat Detail" dengan icon mata di kartu "Total Pengabdian"
2. Klik tombol "Lihat Detail" (atau klik di area kartu mana saja)
3. Modal detail muncul
4. Lihat tabel dengan kolom: No, Judul, Tanggal, Ketua, Sumber Dana, Prodi, Status, Mahasiswa Terlibat
5. Perhatikan informasi mahasiswa (Nama + NIM)
6. Tutup modal

**Kriteria Sukses**:

-   [ ] Tombol "Lihat Detail" mudah ditemukan dan jelas
-   [ ] Icon mata (eye) pada tombol membantu pemahaman
-   [ ] Modal terbuka dalam < 3 detik
-   [ ] Semua data tampil dengan jelas
-   [ ] Informasi mahasiswa (nama + NIM) mudah dibaca
-   [ ] Dapat menutup modal dengan mudah

**SEQ**: Seberapa mudah menyelesaikan task ini? (Skala 1-7)

---

### Task 4: Melihat Detail Dosen Terlibat

**Tujuan**: Menguji akses informasi dosen yang terlibat

**Skenario**:

> "Anda ingin mengetahui dosen mana saja yang terlibat dalam pengabdian dan berapa banyak pengabdian yang mereka lakukan. Klik tombol 'Lihat Detail' pada kartu 'Dosen Terlibat'."

**Langkah yang Diharapkan**:

1. Perhatikan tombol "Lihat Detail" dengan icon mata di kartu "Dosen Terlibat"
2. Klik tombol "Lihat Detail" (atau klik di area kartu mana saja)
3. Modal detail muncul
4. Lihat tabel dosen dengan nama dan jumlah pengabdian
5. Tutup modal

**Kriteria Sukses**:

-   [ ] Tombol "Lihat Detail" mudah ditemukan
-   [ ] Icon pada tombol membantu pemahaman
-   [ ] Modal terbuka dengan cepat
-   [ ] Data dosen mudah dibaca
-   [ ] Jumlah pengabdian per dosen jelas

**SEQ**: Seberapa mudah menyelesaikan task ini? (Skala 1-7)

---

### Task 5: Melihat Pengabdian yang Melibatkan Mahasiswa

**Tujuan**: Menguji akses detail pengabdian dengan mahasiswa

**Skenario**:

> "Anda ingin mengetahui pengabdian mana saja yang melibatkan mahasiswa, termasuk nama dan NIM mahasiswa tersebut. Klik tombol 'Lihat Detail' pada kartu 'Dengan Mahasiswa'."

**Langkah yang Diharapkan**:

1. Perhatikan tombol "Lihat Detail" dengan icon mata di kartu "Dengan Mahasiswa"
2. Klik tombol "Lihat Detail" (atau klik di area kartu mana saja)
3. Modal detail muncul
4. Lihat tabel dengan informasi lengkap
5. Perhatikan kolom "Mahasiswa Terlibat" yang menampilkan nama + NIM
6. Perhatikan juga kolom "Jumlah Mahasiswa"

**Kriteria Sukses**:

-   [ ] Tombol "Lihat Detail" mudah ditemukan
-   [ ] Modal terbuka dengan data lengkap
-   [ ] Nama dan NIM mahasiswa mudah dibaca
-   [ ] Dapat membedakan mahasiswa dari prodi berbeda

**SEQ**: Seberapa mudah menyelesaikan task ini? (Skala 1-7)

---

### Task 6: Menganalisis Chart Dosen Terlibat

**Tujuan**: Menguji pemahaman visualisasi data dosen

**Skenario**:

> "Anda ingin mengetahui dosen mana yang paling produktif dalam pengabdian. Lihat chart 'Dosen Terlibat dalam Pengabdian' dan identifikasi 5 dosen teratas."

**Langkah yang Diharapkan**:

1. Scroll ke bagian chart "Dosen Terlibat dalam Pengabdian"
2. Perhatikan horizontal bar chart
3. Identifikasi 5 dosen dengan jumlah pengabdian tertinggi
4. (Opsional) Klik tombol "Urutkan" untuk mengubah urutan
5. Hover pada bar untuk melihat detail

**Kriteria Sukses**:

-   [ ] Dapat mengidentifikasi dosen teratas dalam < 30 detik
-   [ ] Memahami warna bar (opacity menunjukkan tingkat produktivitas)
-   [ ] Tooltip menampilkan informasi yang jelas

**SEQ**: Seberapa mudah menyelesaikan task ini? (Skala 1-7)

---

### Task 7: Menganalisis Sumber Dana Pengabdian

**Tujuan**: Menguji pemahaman stacked bar chart sumber dana

**Skenario**:

> "Anda ingin membandingkan sumber dana pengabdian tahun ini dengan tahun lalu. Lihat chart 'Sumber Dana Pengabdian' dan identifikasi sumber dana mana yang paling besar kontribusinya."

**Langkah yang Diharapkan**:

1. Scroll ke bagian chart "Sumber Dana Pengabdian"
2. Perhatikan stacked bar chart dengan 2 tahun
3. Hover pada setiap segmen untuk melihat detail
4. Bandingkan total dana antar tahun
5. Identifikasi sumber dana terbesar

**Kriteria Sukses**:

-   [ ] Memahami perbandingan 2 tahun dalam < 1 menit
-   [ ] Tooltip menampilkan nilai rupiah dan persentase
-   [ ] Dapat mengidentifikasi sumber dana utama

**SEQ**: Seberapa mudah menyelesaikan task ini? (Skala 1-7)

---

### Task 8: Menganalisis Jenis Luaran dengan Treemap

**Tujuan**: Menguji pemahaman visualisasi treemap

**Skenario**:

> "Anda ingin mengetahui jenis luaran apa yang paling banyak dihasilkan dari kegiatan pengabdian. Lihat chart 'Jenis Luaran Pengabdian' dan identifikasi 3 jenis luaran teratas."

**Langkah yang Diharapkan**:

1. Scroll ke bagian "Jenis Luaran Pengabdian"
2. Perhatikan treemap visualization
3. Identifikasi kotak terbesar (luaran paling banyak)
4. Hover pada kotak untuk melihat detail
5. Bandingkan ukuran kotak

**Kriteria Sukses**:

-   [ ] Memahami treemap dalam < 1 menit
-   [ ] Dapat membedakan jenis luaran dari ukuran kotak
-   [ ] Tooltip menampilkan jumlah dan persentase

**SEQ**: Seberapa mudah menyelesaikan task ini? (Skala 1-7)

---

### Task 9: Menganalisis KPI dengan Radar Chart

**Tujuan**: Menguji pemahaman KPI radar chart

**Skenario**:

> "Anda ingin mengetahui performa fakultas terhadap indikator KPI pengabdian. Lihat 'KPI Radar Chart' dan identifikasi indikator mana yang sudah mencapai atau melampaui target."

**Langkah yang Diharapkan**:

1. Scroll ke bagian "KPI Radar Chart"
2. Perhatikan radar chart dengan area hijau (capaian) dan garis kuning putus-putus (target)
3. Identifikasi indikator yang capaiannya > 100%
4. Hover pada titik data untuk melihat detail
5. Pahami label singkat di setiap axis

**Kriteria Sukses**:

-   [ ] Memahami radar chart dalam < 2 menit
-   [ ] Dapat mengidentifikasi indikator yang mencapai target
-   [ ] Tooltip menampilkan skor capaian, realisasi, dan target
-   [ ] Label indikator mudah dipahami

**SEQ**: Seberapa mudah menyelesaikan task ini? (Skala 1-7)

---

### Task 10: Menggunakan Filter Tahun

**Tujuan**: Menguji fungsi filter tahun

**Skenario**:

> "Anda ingin melihat data pengabdian untuk tahun 2024 saja. Gunakan filter tahun di bagian atas dashboard."

**Langkah yang Diharapkan**:

1. Temukan dropdown filter tahun di bagian atas
2. Klik dropdown
3. Pilih "2024"
4. Perhatikan semua data dan chart berubah sesuai tahun
5. (Opsional) Kembalikan ke "Semua Tahun"

**Kriteria Sukses**:

-   [ ] Dapat menemukan filter dalam < 15 detik
-   [ ] Filter berfungsi dengan baik
-   [ ] Semua chart dan statistik ter-update
-   [ ] Loading time < 5 detik

**SEQ**: Seberapa mudah menyelesaikan task ini? (Skala 1-7)

---

### Task 11: Mengakses Menu Pengaturan KPI

**Tujuan**: Menguji akses menu pengaturan KPI (fitur baru setelah INQA dihapus)

**Skenario**:

> "Anda ingin melihat atau mengubah pengaturan KPI pengabdian. Temukan dan buka menu Pengaturan KPI."

**Langkah yang Diharapkan**:

1. Lihat sidebar navigasi
2. Temukan menu "Pengaturan KPI" atau "KPI"
3. Klik menu tersebut
4. Halaman pengaturan KPI terbuka
5. Perhatikan daftar indikator KPI yang dapat dikelola

**Kriteria Sukses**:

-   [ ] Menu mudah ditemukan dalam sidebar
-   [ ] Nama menu jelas dan intuitif
-   [ ] Halaman KPI terbuka tanpa error
-   [ ] Interface pengaturan KPI mudah dipahami

**SEQ**: Seberapa mudah menyelesaikan task ini? (Skala 1-7)

---

### Task 12: Melihat Detail KPI Individual

**Tujuan**: Menguji pemahaman detail indikator KPI

**Skenario**:

> "Anda ingin mengetahui detail dari salah satu indikator KPI, seperti target, realisasi, dan perhitungannya. Pilih salah satu KPI dan lihat detailnya."

**Langkah yang Diharapkan**:

1. Di halaman Pengaturan KPI, pilih salah satu indikator
2. Klik untuk melihat detail atau edit
3. Perhatikan informasi: kode KPI, nama indikator, target, satuan, bobot
4. Pahami cara perhitungan skor
5. (Opsional) Kembali ke daftar KPI

**Kriteria Sukses**:

-   [ ] Detail KPI mudah diakses
-   [ ] Informasi lengkap dan jelas
-   [ ] Perhitungan skor dapat dipahami
-   [ ] Navigasi kembali mudah

**SEQ**: Seberapa mudah menyelesaikan task ini? (Skala 1-7)

---

### Task 13: Navigasi Antar Halaman

**Tujuan**: Menguji kemudahan navigasi sistem

**Skenario**:

> "Anda ingin mengeksplorasi sistem. Buka Dashboard, lalu ke Pengaturan KPI, kemudian kembali ke Dashboard."

**Langkah yang Diharapkan**:

1. Dari halaman manapun, klik "Dashboard" di sidebar
2. Dashboard terbuka
3. Klik "Pengaturan KPI" di sidebar
4. Halaman KPI terbuka
5. Klik "Dashboard" lagi untuk kembali

**Kriteria Sukses**:

-   [ ] Navigasi lancar tanpa lag
-   [ ] Menu aktif ter-highlight
-   [ ] Tidak ada broken link
-   [ ] Transisi halaman cepat

**SEQ**: Seberapa mudah menyelesaikan task ini? (Skala 1-7)

---

### Task 14: Logout dari Sistem

**Tujuan**: Menguji proses keluar dari sistem

**Skenario**:

> "Anda telah selesai menggunakan sistem. Silakan logout menggunakan tombol Logout yang tersedia."

**Langkah yang Diharapkan**:

1. Temukan tombol Logout berwarna biru (primary) di kanan atas navbar
2. Klik tombol Logout
3. Modal konfirmasi muncul
4. Klik "Logout" pada modal
5. Sistem kembali ke halaman login

**Kriteria Sukses**:

-   [ ] Tombol Logout mudah ditemukan (terlihat jelas dengan warna biru primary)
-   [ ] Tombol terletak simetris di navbar
-   [ ] Icon logout dan label "Logout" jelas
-   [ ] Modal konfirmasi muncul sebelum logout
-   [ ] Proses logout cepat
-   [ ] Session ter-clear dengan benar
-   [ ] Kembali ke halaman login

**SEQ**: Seberapa mudah menyelesaikan task ini? (Skala 1-7)

---

## 3. SYSTEM USABILITY SCALE (SUS)

**Instruksi**: Berikan penilaian untuk setiap pernyataan berikut dengan skala:

-   1 = Sangat Tidak Setuju
-   2 = Tidak Setuju
-   3 = Netral
-   4 = Setuju
-   5 = Sangat Setuju

| No  | Pernyataan                                                                   | 1   | 2   | 3   | 4   | 5   |
| --- | ---------------------------------------------------------------------------- | --- | --- | --- | --- | --- |
| 1   | Saya berpikir akan menggunakan sistem ini secara berkala                     | ☐   | ☐   | ☐   | ☐   | ☐   |
| 2   | Saya merasa sistem ini terlalu kompleks                                      | ☐   | ☐   | ☐   | ☐   | ☐   |
| 3   | Saya merasa sistem ini mudah digunakan                                       | ☐   | ☐   | ☐   | ☐   | ☐   |
| 4   | Saya memerlukan bantuan teknis untuk menggunakan sistem ini                  | ☐   | ☐   | ☐   | ☐   | ☐   |
| 5   | Saya merasa berbagai fungsi dalam sistem ini terintegrasi dengan baik        | ☐   | ☐   | ☐   | ☐   | ☐   |
| 6   | Saya merasa ada terlalu banyak inkonsistensi dalam sistem ini                | ☐   | ☐   | ☐   | ☐   | ☐   |
| 7   | Saya pikir kebanyakan orang akan belajar menggunakan sistem ini dengan cepat | ☐   | ☐   | ☐   | ☐   | ☐   |
| 8   | Saya merasa sistem ini sangat rumit untuk digunakan                          | ☐   | ☐   | ☐   | ☐   | ☐   |
| 9   | Saya merasa sangat percaya diri menggunakan sistem ini                       | ☐   | ☐   | ☐   | ☐   | ☐   |
| 10  | Saya perlu belajar banyak hal sebelum dapat menggunakan sistem ini           | ☐   | ☐   | ☐   | ☐   | ☐   |

**Cara Menghitung Skor SUS**:

1. Untuk item ganjil (1,3,5,7,9): skor = nilai - 1
2. Untuk item genap (2,4,6,8,10): skor = 5 - nilai
3. Jumlahkan semua skor
4. Kalikan dengan 2.5
5. Skor SUS = Total × 2.5 (range 0-100)

**Interpretasi**:

-   < 50: Sangat Buruk (F)
-   50-60: Buruk (D)
-   60-70: Cukup (C)
-   70-80: Baik (B)
-   80-90: Sangat Baik (A)
-   > 90: Excellent (A+)

---

## 4. USER EXPERIENCE QUESTIONNAIRE (UEQ)

**Instruksi**: Untuk setiap pasangan kata, beri nilai dari -3 hingga +3 yang menggambarkan pengalaman Anda menggunakan sistem.

### Skala Penilaian:

-3: Sangat mendukung kata kiri  
-2: Mendukung kata kiri  
-1: Sedikit mendukung kata kiri  
0: Netral  
+1: Sedikit mendukung kata kanan  
+2: Mendukung kata kanan  
+3: Sangat mendukung kata kanan

---

### **Attractiveness (Daya Tarik)**

| No  | Kata Kiri          | -3  | -2  | -1  | 0   | +1  | +2  | +3  | Kata Kanan   |
| --- | ------------------ | --- | --- | --- | --- | --- | --- | --- | ------------ |
| 1   | Tidak menyenangkan | ☐   | ☐   | ☐   | ☐   | ☐   | ☐   | ☐   | Menyenangkan |
| 2   | Tidak menarik      | ☐   | ☐   | ☐   | ☐   | ☐   | ☐   | ☐   | Menarik      |
| 3   | Tidak ramah        | ☐   | ☐   | ☐   | ☐   | ☐   | ☐   | ☐   | Ramah        |
| 4   | Buruk              | ☐   | ☐   | ☐   | ☐   | ☐   | ☐   | ☐   | Baik         |

---

### **Perspicuity (Kejelasan)**

| No  | Kata Kiri            | -3  | -2  | -1  | 0   | +1  | +2  | +3  | Kata Kanan       |
| --- | -------------------- | --- | --- | --- | --- | --- | --- | --- | ---------------- |
| 5   | Tidak dapat dipahami | ☐   | ☐   | ☐   | ☐   | ☐   | ☐   | ☐   | Dapat dipahami   |
| 6   | Sulit dipelajari     | ☐   | ☐   | ☐   | ☐   | ☐   | ☐   | ☐   | Mudah dipelajari |
| 7   | Rumit                | ☐   | ☐   | ☐   | ☐   | ☐   | ☐   | ☐   | Sederhana        |
| 8   | Membingungkan        | ☐   | ☐   | ☐   | ☐   | ☐   | ☐   | ☐   | Jelas            |

---

### **Efficiency (Efisiensi)**

| No  | Kata Kiri                 | -3  | -2  | -1  | 0   | +1  | +2  | +3  | Kata Kanan               |
| --- | ------------------------- | --- | --- | --- | --- | --- | --- | --- | ------------------------ |
| 9   | Lambat                    | ☐   | ☐   | ☐   | ☐   | ☐   | ☐   | ☐   | Cepat                    |
| 10  | Tidak efisien             | ☐   | ☐   | ☐   | ☐   | ☐   | ☐   | ☐   | Efisien                  |
| 11  | Tidak praktis             | ☐   | ☐   | ☐   | ☐   | ☐   | ☐   | ☐   | Praktis                  |
| 12  | Terorganisir dengan buruk | ☐   | ☐   | ☐   | ☐   | ☐   | ☐   | ☐   | Terorganisir dengan baik |

---

### **Dependability (Keterpercayaan)**

| No  | Kata Kiri              | -3  | -2  | -1  | 0   | +1  | +2  | +3  | Kata Kanan       |
| --- | ---------------------- | --- | --- | --- | --- | --- | --- | --- | ---------------- |
| 13  | Tidak dapat diramalkan | ☐   | ☐   | ☐   | ☐   | ☐   | ☐   | ☐   | Dapat diramalkan |
| 14  | Menghalangi            | ☐   | ☐   | ☐   | ☐   | ☐   | ☐   | ☐   | Mendukung        |
| 15  | Tidak aman             | ☐   | ☐   | ☐   | ☐   | ☐   | ☐   | ☐   | Aman             |
| 16  | Tidak memenuhi harapan | ☐   | ☐   | ☐   | ☐   | ☐   | ☐   | ☐   | Memenuhi harapan |

---

### **Stimulation (Stimulasi)**

| No  | Kata Kiri     | -3  | -2  | -1  | 0   | +1  | +2  | +3  | Kata Kanan   |
| --- | ------------- | --- | --- | --- | --- | --- | --- | --- | ------------ |
| 17  | Membosankan   | ☐   | ☐   | ☐   | ☐   | ☐   | ☐   | ☐   | Menarik      |
| 18  | Tidak menarik | ☐   | ☐   | ☐   | ☐   | ☐   | ☐   | ☐   | Mengasyikkan |
| 19  | Tidak kreatif | ☐   | ☐   | ☐   | ☐   | ☐   | ☐   | ☐   | Kreatif      |
| 20  | Membosankan   | ☐   | ☐   | ☐   | ☐   | ☐   | ☐   | ☐   | Memotivasi   |

---

### **Novelty (Kebaruan)**

| No  | Kata Kiri        | -3  | -2  | -1  | 0   | +1  | +2  | +3  | Kata Kanan |
| --- | ---------------- | --- | --- | --- | --- | --- | --- | --- | ---------- |
| 21  | Konvensional     | ☐   | ☐   | ☐   | ☐   | ☐   | ☐   | ☐   | Inventif   |
| 22  | Biasa            | ☐   | ☐   | ☐   | ☐   | ☐   | ☐   | ☐   | Terdepan   |
| 23  | Konservatif      | ☐   | ☐   | ☐   | ☐   | ☐   | ☐   | ☐   | Inovatif   |
| 24  | Kuno             | ☐   | ☐   | ☐   | ☐   | ☐   | ☐   | ☐   | Mutakhir   |
| 25  | Konvensional     | ☐   | ☐   | ☐   | ☐   | ☐   | ☐   | ☐   | Baru       |
| 26  | Tidak imajinatif | ☐   | ☐   | ☐   | ☐   | ☐   | ☐   | ☐   | Kreatif    |

---

**Cara Menghitung Skor UEQ**:

1. **Per Dimensi**: Jumlahkan nilai item dalam dimensi, bagi dengan jumlah item

    - Attractiveness (Item 1-4): Mean = (Q1+Q2+Q3+Q4)/4
    - Perspicuity (Item 5-8): Mean = (Q5+Q6+Q7+Q8)/4
    - Efficiency (Item 9-12): Mean = (Q9+Q10+Q11+Q12)/4
    - Dependability (Item 13-16): Mean = (Q13+Q14+Q15+Q16)/4
    - Stimulation (Item 17-20): Mean = (Q17+Q18+Q19+Q20)/4
    - Novelty (Item 21-26): Mean = (Q21+Q22+Q23+Q24+Q25+Q26)/6

2. **Interpretasi per Dimensi**:
    - -0.8 hingga +0.8: Evaluasi relatif netral
    - +0.8 hingga +3.0: Evaluasi positif
    - -0.8 hingga -3.0: Evaluasi negatif

---

## 5. PERTANYAAN TERBUKA

### Feedback Umum

1. **Apa yang paling Anda sukai dari sistem ini?**

    _Jawaban:_

    ***

2. **Apa yang paling membuat Anda kesulitan dalam menggunakan sistem ini?**

    _Jawaban:_

    ***

3. **Fitur apa yang menurut Anda paling berguna sebagai Dekan?**

    _Jawaban:_

    ***

4. **Apakah ada fitur yang menurut Anda kurang atau perlu ditambahkan?**

    _Jawaban:_

    ***

5. **Bagaimana pendapat Anda tentang visualisasi data (chart, graph) yang disediakan?**

    _Jawaban:_

    ***

### Feedback Spesifik - Dashboard

6. **Apakah statistik di dashboard memberikan informasi yang Anda butuhkan?**

    _Jawaban:_

    ***

7. **Apakah modal detail (popup) saat klik kartu statistik mudah dipahami?**

    _Jawaban:_

    ***

8. **Bagaimana pendapat Anda tentang KPI Radar Chart? Apakah mudah dipahami?**

    _Jawaban:_

    ***

### Feedback Spesifik - Pengaturan KPI

9. **Apakah menu Pengaturan KPI mudah diakses?**

    _Jawaban:_

    ***

10. **Apakah informasi di halaman Pengaturan KPI sudah lengkap dan jelas?**

    _Jawaban:_

    ***

11. **Apakah Anda memerlukan fitur tambahan untuk mengelola KPI?**

    _Jawaban:_

    ***

### Saran Perbaikan

12. **Saran perbaikan untuk tampilan (UI/UX)?**

    _Jawaban:_

    ***

13. **Saran perbaikan untuk fitur atau fungsionalitas?**

    _Jawaban:_

    ***

14. **Saran perbaikan untuk performa sistem?**

    _Jawaban:_

    ***

---

## 6. OBSERVASI MODERATOR

**Instruksi untuk Moderator**: Catat pengamatan Anda selama responden melakukan task

### Task Completion Time

| Task                         | Target Time      | Actual Time | Status (✓/✗) | Catatan |
| ---------------------------- | ---------------- | ----------- | ------------ | ------- |
| Task 1: Login                | < 1 menit        |             |              |         |
| Task 2: Lihat Statistik      | < 30 detik       |             |              |         |
| Task 3: Detail Pengabdian    | < 3 detik (load) |             |              |         |
| Task 4: Detail Dosen         | < 3 detik (load) |             |              |         |
| Task 5: Detail Mahasiswa     | < 3 detik (load) |             |              |         |
| Task 6: Analisis Chart Dosen | < 30 detik       |             |              |         |
| Task 7: Analisis Sumber Dana | < 1 menit        |             |              |         |
| Task 8: Analisis Treemap     | < 1 menit        |             |              |         |
| Task 9: Analisis KPI Radar   | < 2 menit        |             |              |         |
| Task 10: Filter Tahun        | < 15 detik       |             |              |         |
| Task 11: Menu KPI            | < 15 detik       |             |              |         |
| Task 12: Detail KPI          | < 1 menit        |             |              |         |
| Task 13: Navigasi            | < 30 detik       |             |              |         |
| Task 14: Logout              | < 15 detik       |             |              |         |

### Error/Issue Log

| Timestamp | Task | Error/Issue | Severity (High/Med/Low) | Screenshot/Notes |
| --------- | ---- | ----------- | ----------------------- | ---------------- |
|           |      |             |                         |                  |
|           |      |             |                         |                  |
|           |      |             |                         |                  |

### User Behavior Notes

**Positive Behaviors**:

-
-
-   **Negative Behaviors/Frustrations**:

-
-
-   **Unexpected Actions**:

-
-
-   ***

## 7. HASIL DAN REKOMENDASI

### Ringkasan Skor

| Metrik                   | Skor         | Interpretasi |
| ------------------------ | ------------ | ------------ |
| **SEQ Average**          | \_\_\_ / 7   |              |
| **SUS Score**            | \_\_\_ / 100 |              |
| **UEQ - Attractiveness** | \_\_\_       |              |
| **UEQ - Perspicuity**    | \_\_\_       |              |
| **UEQ - Efficiency**     | \_\_\_       |              |
| **UEQ - Dependability**  | \_\_\_       |              |
| **UEQ - Stimulation**    | \_\_\_       |              |
| **UEQ - Novelty**        | \_\_\_       |              |

### Task Success Rate

-   **Jumlah Task**: 14
-   **Task Berhasil**: \_\_\_
-   **Task Gagal**: \_\_\_
-   **Success Rate**: \_\_\_\_%

### Prioritas Perbaikan

**High Priority** (Harus diperbaiki):

1.
2.
3.

**Medium Priority** (Sebaiknya diperbaiki):

1.
2.
3.

**Low Priority** (Nice to have):

1.
2.
3.

### Kesimpulan

_[Tulis kesimpulan keseluruhan dari pengujian]_

---

## 8. LAMPIRAN

### Lampiran A: Screenshots

-   Screenshot Dashboard
-   Screenshot Modal Detail
-   Screenshot Chart
-   Screenshot KPI Settings
-   Screenshot Error (jika ada)

### Lampiran B: Recording

-   Link video recording session: **\*\***\_\_\_**\*\***

### Lampiran C: Consent Form

-   Informed consent dari responden: ☐ Sudah ditandatangani

---

**Tanggal Pengujian**: **\*\***\_\_\_**\*\***  
**Moderator**: **\*\***\_\_\_**\*\***  
**Responden**: **\*\***\_\_\_**\*\***  
**Durasi Pengujian**: **\*\***\_\_\_**\*\*** menit
