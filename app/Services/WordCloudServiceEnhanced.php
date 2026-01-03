<?php

namespace App\Services;

use Sastrawi\Stemmer\StemmerFactory;

class WordCloudServiceEnhanced
{
    private array $config;
    private $stemmer;

    // A) Priority Tokens (Strong Boost x2)
    // Thematic words that should stand out
    private array $whitelistStrong = [
        'kewirausahaan', 'robotika', 'iot', 'pemrograman', 'aksara', 'batik',
        'dashboard', 'database', 'website', 'multimedia', 'umkm',
        'keuangan', 'perpustakaan', 'kepemimpinan'
    ];
    
    // B) Protected Tokens (Standard/Weak Boost x1)
    // Important context words that are protected from removal but NOT boosted
    // to prevent them from dominating the cloud (e.g. "data", "komputer")
    private array $whitelistProtected = [
        'komputer', 'digital', 'elearning', 'google', 
        'olimpiade', 'android', 'aplikasi', 'sistem', 'teknologi',
        'media', 'inovasi', 'sosial', 
    ];

    // C) Stopwords
    private array $stopwords = [
        // 1. Education / Institution / Admin Terms (New & Expanded)
        'yayasan', 'direktorat', 'unit', 'administrasi', 'dokumen', 'surat', 
        'bernadus', 'sanjaya', 'kolese', 'sekolah', 'madrasah', 'kampus', 
        'universitas', 'fakultas', 'prodi', 'kelas', 'sma', 'sman', 'smp', 'sd', 
        'tk', 'paud', 'smk', 'man', 'mts', 'mi', 'ukdw', 'kristen', 'duta', 'wacana',
        'lppm', 'humas', 'biro', 'bagian', 'divisi', 'ketua', 'sekretaris', 'kepala',
        'budya', 'menengah', 'atas', 'kurikulum', 'smpn', 'duce', 'stella',

        // 2. Generic Activity/Process
        'pendampingan', 'pelatihan', 'kegiatan', 'program', 'workshop', 'webinar', 'sosialisasi',
        'ceramah', 'seminar', 'lomba', 'kampanye', 'penyuluhan', 'bimbingan', 'belajar',
        'kuliah', 'kerja', 'nyata', 'kkn', 'abdimas', 'pengabdian', 'pembelajaran',
        'online', 'video', 'penulisan', 'ken',

        // 3. Implementation Verbs / Academic Filler
        'pembuatan', 'pengembangan', 'penerapan', 'pengaplikasian', 'peningkatan',
        'penguatan', 'persiapan', 'penyusunan', 'pengelolaan', 'pemetaan', 'setup',
        'perancangan', 'implementasi', 'optimalisasi', 'analisis', 'evaluasi', 'monitoring',
        'manajemen', 'pemeliharaan', 'instalasi', 'pemanfaatan', 'upaya', 'strategi',
        'metode', 'model', 'sistem', 'aplikasi', 'teknologi', 'informasi',

        // 4. Connectors / Operational
        'berbasis', 'untuk', 'pada', 'di', 'dan', 'dengan', 'bagi', 'dalam', 'melalui',
        'sebagai', 'tingkat', 'dasar', 'lanjutan', 'menuju', 'menggunakan', 'secara',
        'terhadap', 'tentang', 'serta', 'oleh', 'kepada', 'yang', 'dari', 'ini', 'itu',
        'juga', 'tidak', 'adalah', 'yaitu', 'agar', 'supaya', 'guna', 'demi',
        'bagi', 'para', 'mitra',

        // 5. Institution / Organization Names (Legacy)
        'gkj', 'sinode', 'klasis', 'paroki', 'bopkri', 'kanisius', 'britto', 
        'mission21', 'santa', 'maria', 'assumpta', 'muhammadiyah', 'aisyiyah',
        'pkk', 'posyandu', 'karang', 'taruna', 'rt', 'rw', 'dusun', 'desa', 'kecamatan',
        'kabupaten', 'kota', 'provinsi', 'kelurahan', 'wirogunan', 'mergangsan', 'wirobrajan', 'rogomulyo',

        // 6. Location Names
        'yogyakarta', 'klaten', 'magelang', 'purworejo', 'wates', 'pandak', 'sleman',
        'bantul', 'gunungkidul', 'kulon', 'progo', 'jawa', 'tengah', 'timur', 'barat',
        'indonesia', 'diy', 'solo', 'surakarta', 'boyolali', 'sragen', 'sukoharjo',
        'wonogiri', 'karanganyar', 'salatiga', 'semarang', 'tempel', 'sanding', 'purwosari',
        
        // 7. Misc / People
        'masyarakat', 'warga', 'kelompok', 'orang', 'tua',
        'anak', 'usia', 'dini', 'remaja', 'pemuda', 'ibu', 'bapak', 'karyawan',
        'ummat', 'jemaat', 'gereja', 'majelis', 'pengurus', 'anggota',
        'siswa', 'mahasiswa', 'guru', 'osis',
        'umat', 'tani', 'sedyo', 'makmur', 'gabungan', 'pegawai',
        
        // 8. Specific Ambiguous/Noise (User Requested)
        'roda', 'kursi', 'lanjut', 'medari', 'lapor', 'catat', 'proses', 'bentuk', 
        'standar', 'data', 'kodular', 'bina', 'jatimulyo', 'pikir', 'gematen', 
        'pirus', 'hasil', 'asia', 'logika', 'daya', 'langgan', 'profil', 'lahan', 'kevikepan', 'buka', 'mukiran', 'rosario', 'sepex', 'petrus', 'santo','laksana', 'pekan', 'uskup', 'ngudi',
        
        // Variations/Stems
        'laporan', 'pencatatan', 'catatan', 'pembentukan', 'pemrosesan', 
        'lanjutan', 'tingkat_lanjut', 'berpikir', 'pemikiran',
        
        // 9. Normalized Noise
        'baru', 'bicara', 'bidang', 'mampu', 'siswi', 'kenal', 'sama',
        'sedia', 'tersedia', 'ketersediaan', 'penyediaan', 'badan', 'makan', 'abad',
        
        // 10. Residual Fragments (Final Guarantee)
        'jaring', 'layan', 'bangun', 'sembah',
        'lingkung', 'kembang', 'bantu',
    ];

    // Synonym Map
    private array $synonymMap = [
        // Normalization (User Requested)
        'sibaru' => 'registrasi',
        
        // Safeguard Mappings
        'layan' => 'layanan', 
        'pelayanan' => 'layanan', 
        'melayani' => 'layanan',
        
        'jaring' => 'jaringan', 
        'jejaring' => 'jaringan',
        'menjaring' => 'jaringan',
        
        'bangun' => 'pembangunan', 
        'membangun' => 'pembangunan',
        'pembangun' => 'pembangunan',
        
        'sembah' => 'persembahan',
        'persembahan' => 'persembahan',
        
        'lingkung' => 'lingkungan',
        'kembang' => 'perkembangan',
        'bantu' => 'bantuan',

        // Tech Terms
        'iot' => 'iot', 'internet of things' => 'iot',
        'elearning' => 'elearning', 'e-learning' => 'elearning',
        'webservice' => 'webservice', 'web service' => 'webservice',
        'google suite' => 'google', 'google workspace' => 'google',
        'google apps' => 'google', 'google form' => 'google', 'google forms' => 'google',
        'gsuite' => 'google',
        'c++' => 'cpp', 'cpp' => 'cpp', 'c#' => 'csharp', 'vb.net' => 'vbnet',
        'entrepreneurship' => 'kewirausahaan', 'kewirausahaan' => 'kewirausahaan', 
        'wirausaha' => 'kewirausahaan', 'bisnis' => 'kewirausahaan',
        'leadership' => 'kepemimpinan', 'kepemimpinan' => 'kepemimpinan',
        'slims' => 'slims', 'senayan library' => 'slims',
        'batik' => 'batik', 'membatik' => 'batik',
        'aksara' => 'aksara', 'hanacaraka' => 'aksara',
        'keuangan' => 'keuangan', 'finansial' => 'keuangan', 'akuntansi' => 'keuangan',
        'perpustakaan' => 'perpustakaan', 'pustaka' => 'perpustakaan',
        'umkm' => 'umkm', 'usahamikrokecilmenengah' => 'umkm', 'ukm' => 'umkm',
        'web' => 'website', 'situs' => 'website', 'portal' => 'website',
        'db' => 'database', 'basis data' => 'database', 'basisdata' => 'database',
        'hp' => 'smartphone', 'handphone' => 'smartphone', 'ponsel' => 'smartphone',
        'mobile' => 'smartphone', 
    ];

    public function __construct(array $config = [])
    {
        $this->config = array_merge([
            'minTokenLength' => 3,   
            'minCount' => 2,
            'maxWords' => 45,
            
            // B) Stemming Fix: Disabled (Safeguard 1)
            'applyStemming' => false, 
            
            'useBigrams' => false,
            'useTrigrams' => false,
            
            // Scaling Config
            'enableScaling' => true,
            'minFontSize' => 14,
            'maxFontSize' => 50, // Clamped max
        ], $config);

        if ($this->config['applyStemming']) {
            $stemmerFactory = new StemmerFactory();
            $this->stemmer = $stemmerFactory->createStemmer();
        }
    }

    /**
     * Process word cloud from array of titles
     */
    public function processWordCloud(array $titles): array
    {
        if (empty($titles)) return [];

        // 1. Extract and Normalize
        $tokenCounts = $this->extractTokens($titles);
        
        // 3) Safeguard: Hard Merge Residuals
        $tokenCounts = $this->mergeResidualTokens($tokenCounts);

        // 2. Filter and Score
        // Try strict filter (minCount=2)
        $result = $this->filterAndScore($tokenCounts, (int)$this->config['minCount']);

        // C) Prevent Empty Cloud
        // If result < 12 items, fallback to minCount=1
        if (count($result) < 12) {
             $result = $this->filterAndScore($tokenCounts, 1);
        }

        // Limit results
        $result = array_slice($result, 0, (int)$this->config['maxWords']);
        
        // D) Visual Scaling
        if ($this->config['enableScaling'] && !empty($result)) {
            $result = $this->applyScaling($result);
        }

        return $result;
    }

    private function extractTokens(array $titles): array
    {
        $counts = [];

        foreach ($titles as $title) {
            $words = $this->tokenize((string)$title);

            foreach ($words as $word) {
                // Synonym Map (Safeguard 2)
                $word = $this->synonymMap[$word] ?? $word;
                
                // Identify Whitelist Status
                $isStrong = in_array($word, $this->whitelistStrong, true);
                $isProtected = in_array($word, $this->whitelistProtected, true);
                $isWhitelisted = $isStrong || $isProtected;

                // Stopword Filtering (Safeguard 4)
                if (!$isWhitelisted) {
                    if (in_array($word, $this->stopwords, true)) continue;
                    if (mb_strlen($word, 'UTF-8') < $this->config['minTokenLength']) continue;
                    if (preg_match('/\d/', $word)) continue;
                }

                // Stemming (Safeguard 1 logic check)
                if ($this->config['applyStemming'] && !$isWhitelisted) {
                    $stemmed = $this->stemmer->stem($word);
                    // Post-stem checks
                    if (in_array($stemmed, $this->stopwords, true)) continue;
                    // ...
                    $word = $stemmed;
                }
                
                // Final count
                $counts[$word] = ($counts[$word] ?? 0) + 1;
            }
        }

        return $counts;
    }
    
    /**
     * Safeguard 3: Merge specific partial tokens into full forms
     */
    private function mergeResidualTokens(array $counts): array
    {
        // Merge 'jaring' -> 'jaringan'
        if (isset($counts['jaring'])) {
            $counts['jaringan'] = ($counts['jaringan'] ?? 0) + $counts['jaring'];
            unset($counts['jaring']);
        }
        
        // Merge 'layan' -> 'layanan'
        if (isset($counts['layan'])) {
            $counts['layanan'] = ($counts['layanan'] ?? 0) + $counts['layan'];
            unset($counts['layan']);
        }
        
        // Merge 'bangun' -> 'pembangunan'
        if (isset($counts['bangun'])) {
            $counts['pembangunan'] = ($counts['pembangunan'] ?? 0) + $counts['bangun'];
            unset($counts['bangun']);
        }

        // Merge 'sembah' -> 'persembahan'
        if (isset($counts['sembah'])) {
            $counts['persembahan'] = ($counts['persembahan'] ?? 0) + $counts['sembah'];
            unset($counts['sembah']);
        }

        // Merge 'lingkung' -> 'lingkungan'
        if (isset($counts['lingkung'])) {
            $counts['lingkungan'] = ($counts['lingkungan'] ?? 0) + $counts['lingkung'];
            unset($counts['lingkung']);
        }
        
        // Merge 'kembang' -> 'perkembangan'
        if (isset($counts['kembang'])) {
            $counts['perkembangan'] = ($counts['perkembangan'] ?? 0) + $counts['kembang'];
            unset($counts['kembang']);
        }
        
        // Merge 'bantu' -> 'bantuan'
        if (isset($counts['bantu'])) {
            $counts['bantuan'] = ($counts['bantuan'] ?? 0) + $counts['bantu'];
            unset($counts['bantu']);
        }
        
        return $counts;
    }

    private function tokenize(string $text): array
    {
        $text = mb_strtolower($text, 'UTF-8');
        $text = str_ireplace(['c++', 'c#'], ['cpp', 'csharp'], $text);
        $text = preg_replace('/[^a-z0-9\s]/u', ' ', $text);
        $words = preg_split('/\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);
        $prefixes = ['ibm', 'ibk', 'pkm', 'ppkm', 'kkn'];
        return array_values(array_filter($words, fn($w) => !in_array($w, $prefixes, true)));
    }

    private function filterAndScore(array $counts, int $minCount): array
    {
        $result = [];
        
        foreach ($counts as $word => $count) {
            // Whitelist Check
            $isStrong = in_array($word, $this->whitelistStrong, true);
            $isProtected = in_array($word, $this->whitelistProtected, true);
            $isWhitelisted = $isStrong || $isProtected;
            
            // MinCount Check (Whitelisted always pass if count >= 1)
            $required = $isWhitelisted ? 1 : $minCount;
            if ($count < $required) continue;
            
            // Score Calculation
            $score = $count;
            if ($isStrong) {
                // E) Strong Boost (x2)
                $score *= 2; 
            }
            
            $result[] = [
                'word' => $word,
                'count' => $score, 
                'raw_count' => $count,
                'is_whitelist' => $isWhitelisted
            ];
        }

        // Sort by final score
        usort($result, function ($a, $b) {
            if ($a['count'] === $b['count']) return 0;
            return ($a['count'] > $b['count']) ? -1 : 1;
        });

        return $result;
    }

    /**
     * D) Visual Scaling: Logarithmic scale clamped to font sizes
     */
    private function applyScaling(array $data): array
    {
        if (empty($data)) return [];

        $scores = array_column($data, 'count');
        $minScore = min($scores);
        $maxScore = max($scores);

        $minFont = $this->config['minFontSize'];
        $maxFont = $this->config['maxFontSize'];

        // Avoid division by zero if all scores are same
        if ($minScore === $maxScore) {
             foreach ($data as &$item) {
                 $item['count'] = (int)(($minFont + $maxFont) / 2);
             }
             return $data;
        }

        foreach ($data as &$item) {
            // Log Scale
            $score = $item['count'];
            $score = max(1, $score);
            
            $logScore = log($score);
            $logMin = log(max(1, $minScore));
            $logMax = log($maxScore);
            
            if ($logMax == $logMin) {
                $scale = 0.5;
            } else {
                $scale = ($logScore - $logMin) / ($logMax - $logMin);
            }
            
            $fontSize = $minFont + ($scale * ($maxFont - $minFont));
            
            $item['count'] = (int)$fontSize;
        }

        return $data;
    }

    public function addStopwords(array $words): void {
        $this->stopwords = array_merge($this->stopwords, $words);
    }
}
