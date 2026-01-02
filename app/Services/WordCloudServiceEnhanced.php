<?php

namespace App\Services;

use Sastrawi\Stemmer\StemmerFactory;

class WordCloudServiceEnhanced
{
    private array $config;
    private $stemmer;

    // Expanded stopwords list
    private array $stopwords = [
        // Basic stopwords
        'yang','dan','di','dari','ke','pada','untuk','dengan','dalam','oleh',
        'sebagai','adalah','ini','itu','akan','telah','sudah','dapat','bisa',
        'ada','atau','juga','tidak','belum','masih','harus','bagi','antara',
        'serta','seperti','karena','namun','tetapi','jika','bila','maka',
        'saat','ketika','dimana','bagaimana','mengapa','siapa','apa','kapan',

        // Pronouns
        'saya','aku','kamu','anda','dia','mereka','kami','kita','ia','nya',
        'mu','ku','beliau',

        // Informal terms
        'yg','dgn','dg','utk','pd','krn','jd','sdh','blm','tdk','gak',

        // Common connectors
        'melalui','terhadap','tentang','mengenai','hingga','sampai','sejak',
        'selama','setelah','sebelum','sesudah',

        // Articles and particles
        'para','sang','si','nya','lah','kah','pun',

        // Common verbs that don't add value
        'membuat','melakukan','menggunakan','memberikan','menjadi','memiliki',
        'mendapatkan','menunjukkan','meningkatkan','mengembangkan',

        // Academic filler words
        'penelitian','studi','kajian','analisis','evaluasi','implementasi',
        'pengembangan','peningkatan','penerapan','pembangunan','pembuatan',
        'perancangan','desain','design',

        // Common prepositions
        'atas','bawah','depan','belakang','samping','luar','tengah',
    ];

    // Blocklist for names, organizations, and places (editable)
    private array $blocklist = [
        // Common Indonesian names
        'ahmad','budi','citra','dewi','eka','fajar','gita','hadi','indra',
        'joko','kartika','lestari','maya','nugroho','putri','rini','sari',
        'taufik','utami','wati','yanto','zainal',

        // Common organizations
        'universitas','institut','sekolah','fakultas','jurusan','prodi',
        'departemen','kementerian','dinas','badan','lembaga','yayasan',

        // Common places
        'jakarta','bandung','surabaya','medan','semarang','yogyakarta',
        'malang','solo','denpasar','makassar','palembang','tangerang',
        'bekasi','depok','bogor',

        // Generic terms
        'berbasis','menggunakan','terhadap','melalui','dengan',

        // Tambahan yang sering bikin noise di dataset pengabdian
        'mission','mission21','britto','pej',
    ];

    // Synonym map for word normalization
    private array $synonymMap = [
        'aplikasi' => 'aplikasi',
        'app' => 'aplikasi',
        'application' => 'aplikasi',

        'sistem' => 'sistem',
        'system' => 'sistem',

        'teknologi' => 'teknologi',
        'technology' => 'teknologi',
        'tech' => 'teknologi',

        'informasi' => 'informasi',
        'information' => 'informasi',
        'info' => 'informasi',

        'data' => 'data',
        'dataset' => 'data',

        'web' => 'web',
        'website' => 'web',
        'situs' => 'web',

        'mobile' => 'mobile',
        'smartphone' => 'mobile',
        'handphone' => 'mobile',

        'pembelajaran' => 'pembelajaran',
        'learning' => 'pembelajaran',
        'belajar' => 'pembelajaran',

        'manajemen' => 'manajemen',
        'management' => 'manajemen',
        'pengelolaan' => 'manajemen',

        'monitoring' => 'monitoring',
        'pemantauan' => 'monitoring',

        'digital' => 'digital',
        'digitalisasi' => 'digital',

        'online' => 'online',
        'daring' => 'online',

        'komunitas' => 'komunitas',
        'community' => 'komunitas',
        'masyarakat' => 'komunitas',

        // tambahan relevan
        'suite' => 'workspace',      // google suite -> workspace
        'workspace' => 'workspace',
        'e-learning' => 'elearning',
        'elearning' => 'elearning',
        'c++' => 'cpp',
        'cpp' => 'cpp',
        'c#' => 'csharp',
        'csharp' => 'csharp',
    ];

    public function __construct(array $config = [])
    {
        $this->config = array_merge([
            'minTokenLength' => 4,
            'minCount' => 2,

            // ✅ baru: frasa tetap ketat meski dataset kecil
            'phraseMinCount' => 2,
            'removeSingleOccurrence' => true,
            'removeSingleOccurrencePhrases' => true,

            'maxWords' => 50,
            'maxBigrams' => 20,
            'maxTrigrams' => 10,

            'applyStemming' => true,
            'useBigrams' => true,
            'useTrigrams' => true,
            'phraseFirst' => true,

            'smallDatasetThreshold' => 50,

            // ✅ baru: blok token yang mengandung angka (mission21)
            'banTokensWithDigits' => true,

            // ✅ baru: whitelist token pendek yang masih relevan
            'shortTokenWhitelist' => ['web','iot','ai','vr','ar','ux','ui','pos','ml','cv'],
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

        $isSmallDataset = count($titles) < (int)$this->config['smallDatasetThreshold'];

        // token boleh lebih longgar untuk dataset kecil
        $tokenMinCount = $isSmallDataset ? 1 : (int)$this->config['minCount'];
        $removeSinglesToken = $isSmallDataset ? false : (bool)$this->config['removeSingleOccurrence'];

        // frasa tetap ketat walau dataset kecil
        $phraseMinCount = max(2, (int)$this->config['phraseMinCount']);
        $removeSinglesPhrase = (bool)$this->config['removeSingleOccurrencePhrases'];

        // Extract single tokens
        $tokens = $this->extractTokens($titles);

        // Extract bigrams/trigrams
        $bigrams = $this->config['useBigrams'] ? $this->extractNGrams($titles, 2) : [];
        $trigrams = $this->config['useTrigrams'] ? $this->extractNGrams($titles, 3) : [];

        // Combine and prioritize
        if ($this->config['phraseFirst']) {
            $result = array_merge(
                $this->filterAndSort($trigrams, $phraseMinCount, (int)$this->config['maxTrigrams'], $removeSinglesPhrase),
                $this->filterAndSort($bigrams,  $phraseMinCount, (int)$this->config['maxBigrams'],  $removeSinglesPhrase),
                $this->filterAndSort($tokens,   $tokenMinCount,  (int)$this->config['maxWords'],    $removeSinglesToken),
            );
        } else {
            // Mix all together (kurang disarankan untuk judul pengabdian)
            $combined = array_merge($tokens, $bigrams, $trigrams);
            $result = $this->filterAndSort($combined, $tokenMinCount, (int)$this->config['maxWords'], $removeSinglesToken);
        }

        return array_slice($result, 0, (int)$this->config['maxWords']);
    }

    /**
     * Extract and count single word tokens
     */
    private function extractTokens(array $titles): array
    {
        $tokenCounts = [];

        foreach ($titles as $title) {
            $words = $this->tokenize((string)$title);

            foreach ($words as $word) {
                // validasi awal
                if (!$this->isValidToken($word)) continue;

                // normalize (stemming + synonym)
                $word = $this->normalizeToken($word);

                // ✅ re-check setelah normalize
                if (!$this->isValidToken($word)) continue;

                $tokenCounts[$word] = ($tokenCounts[$word] ?? 0) + 1;
            }
        }

        return $tokenCounts;
    }

    /**
     * Extract n-grams (bigrams or trigrams)
     */
    private function extractNGrams(array $titles, int $n): array
    {
        $ngramCounts = [];

        foreach ($titles as $title) {
            $words = $this->tokenize((string)$title);

            // Filter valid words first
            $validWords = array_values(array_filter($words, fn($w) => $this->isValidToken($w)));

            for ($i = 0; $i <= count($validWords) - $n; $i++) {
                $ngram = array_slice($validWords, $i, $n);

                // normalize tiap token + re-check
                $ngram = array_map(fn($w) => $this->normalizeToken($w), $ngram);

                foreach ($ngram as $w) {
                    if (!$this->isValidToken($w)) {
                        continue 2; // skip n-gram kalau ada 1 token invalid
                    }
                }

                $ngramStr = implode(' ', $ngram);
                $ngramCounts[$ngramStr] = ($ngramCounts[$ngramStr] ?? 0) + 1;
            }
        }

        return $ngramCounts;
    }

    /**
     * Tokenize text into words
     */
    private function tokenize(string $text): array
    {
        $text = mb_strtolower($text, 'UTF-8');

        // normalisasi cepat beberapa istilah supaya tidak pecah
        $text = str_ireplace(['c++', 'c#'], ['cpp', 'csharp'], $text);

        // keep letters, numbers, spaces
        $text = preg_replace('/[^a-z0-9\s]/u', ' ', $text);
        $words = preg_split('/\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);

        return $words ?: [];
    }

    /**
     * Normalize token: stemming + synonym
     */
    private function normalizeToken(string $token): string
    {
        $token = trim($token);

        if ($token === '') return $token;

        if ($this->config['applyStemming']) {
            $token = $this->stemmer->stem($token);
        }

        return $this->normalizeSynonym($token);
    }

    /**
     * Check if token is valid
     */
    private function isValidToken(string $token): bool
    {
        $token = trim($token);
        if ($token === '') return false;

        // blok token yang mengandung angka (mission21, rw18, etc) jika aktif
        if (!empty($this->config['banTokensWithDigits']) && preg_match('/\d/', $token)) {
            return false;
        }

        // stopwords & blocklist
        if (in_array($token, $this->stopwords, true)) return false;
        if (in_array($token, $this->blocklist, true)) return false;

        // must contain letter
        if (!preg_match('/[a-z]/i', $token)) return false;

        // length filter (allow whitelist token pendek)
        $minLen = (int)($this->config['minTokenLength'] ?? 4);
        $whitelist = $this->config['shortTokenWhitelist'] ?? [];
        if (mb_strlen($token, 'UTF-8') < $minLen && !in_array($token, $whitelist, true)) {
            return false;
        }

        return true;
    }

    /**
     * Normalize synonyms
     */
    private function normalizeSynonym(string $word): string
    {
        return $this->synonymMap[$word] ?? $word;
    }

    /**
     * Filter by count and sort
     */
    private function filterAndSort(array $counts, int $minCount, int $limit, bool $removeSingles): array
    {
        $filtered = array_filter($counts, fn($count) => $count >= $minCount);

        if ($removeSingles) {
            $filtered = array_filter($filtered, fn($count) => $count > 1);
        }

        arsort($filtered);

        $result = [];
        foreach ($filtered as $text => $count) {
            $result[] = [
                'word' => $text,
                'count' => $count
            ];
        }

        return array_slice($result, 0, $limit);
    }

    public function addStopwords(array $words): void
    {
        $this->stopwords = array_values(array_unique(array_merge($this->stopwords, $words)));
    }

    public function addBlocklist(array $items): void
    {
        $this->blocklist = array_values(array_unique(array_merge($this->blocklist, $items)));
    }

    public function addSynonyms(array $synonyms): void
    {
        $this->synonymMap = array_merge($this->synonymMap, $synonyms);
    }
}
