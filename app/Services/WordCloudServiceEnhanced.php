<?php

namespace App\Services;

use Sastrawi\Stemmer\StemmerFactory;

class WordCloudServiceEnhanced
{
    private $config;
    private $stemmer;
    
    // Expanded stopwords list
    private $stopwords = [
        // Basic stopwords
        'yang', 'dan', 'di', 'dari', 'ke', 'pada', 'untuk', 'dengan', 'dalam', 'oleh',
        'sebagai', 'adalah', 'ini', 'itu', 'akan', 'telah', 'sudah', 'dapat', 'bisa',
        'ada', 'atau', 'juga', 'tidak', 'belum', 'masih', 'harus', 'bagi', 'antara',
        'serta', 'seperti', 'karena', 'namun', 'tetapi', 'jika', 'bila', 'maka',
        'saat', 'ketika', 'dimana', 'bagaimana', 'mengapa', 'siapa', 'apa', 'kapan',
        
        // Pronouns
        'saya', 'aku', 'kamu', 'anda', 'dia', 'mereka', 'kami', 'kita', 'ia', 'nya',
        'mu', 'ku', 'beliau',
        
        // Informal terms
        'yg', 'dgn', 'dg', 'utk', 'pd', 'krn', 'jd', 'sdh', 'blm', 'tdk', 'gak',
        
        // Common connectors
        'melalui', 'terhadap', 'tentang', 'mengenai', 'hingga', 'sampai', 'sejak',
        'selama', 'setelah', 'sebelum', 'sesudah',
        
        // Articles and particles
        'para', 'sang', 'si', 'nya', 'lah', 'kah', 'pun',
        
        // Common verbs that don't add value
        'membuat', 'melakukan', 'menggunakan', 'memberikan', 'menjadi', 'memiliki',
        'mendapatkan', 'menunjukkan', 'meningkatkan', 'mengembangkan',
        
        // Academic filler words
        'penelitian', 'studi', 'kajian', 'analisis', 'evaluasi', 'implementasi',
        'pengembangan', 'peningkatan', 'penerapan', 'pembangunan', 'pembuatan',
        'perancangan', 'desain', 'design',
        
        // Common prepositions
        'atas', 'bawah', 'depan', 'belakang', 'samping', 'luar', 'tengah',
    ];
    
    // Blocklist for names, organizations, and places (editable)
    private $blocklist = [
        // Common Indonesian names
        'ahmad', 'budi', 'citra', 'dewi', 'eka', 'fajar', 'gita', 'hadi', 'indra',
        'joko', 'kartika', 'lestari', 'maya', 'nugroho', 'putri', 'rini', 'sari',
        'taufik', 'utami', 'wati', 'yanto', 'zainal',
        
        // Common organizations
        'universitas', 'institut', 'sekolah', 'fakultas', 'jurusan', 'prodi',
        'departemen', 'kementerian', 'dinas', 'badan', 'lembaga', 'yayasan',
        
        // Common places
        'jakarta', 'bandung', 'surabaya', 'medan', 'semarang', 'yogyakarta',
        'malang', 'solo', 'denpasar', 'makassar', 'palembang', 'tangerang',
        'bekasi', 'depok', 'bogor',
        
        // Generic terms
        'berbasis', 'menggunakan', 'terhadap', 'melalui', 'dengan',
    ];
    
    // Synonym map for word normalization
    private $synonymMap = [
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
    ];

    public function __construct(array $config = [])
    {
        $this->config = array_merge([
            'minTokenLength' => 4,
            'minCount' => 2,
            'maxWords' => 50,
            'maxBigrams' => 20,
            'maxTrigrams' => 10,
            'applyStemming' => true,
            'useBigrams' => true,
            'useTrigrams' => true,
            'phraseFirst' => true,
            'removeSingleOccurrence' => true,
            'smallDatasetThreshold' => 50,
        ], $config);
        
        // Initialize Sastrawi stemmer
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
        if (empty($titles)) {
            return [];
        }
        
        $isSmallDataset = count($titles) < $this->config['smallDatasetThreshold'];
        
        // Adjust minCount for small datasets
        $minCount = $isSmallDataset ? max(1, $this->config['minCount'] - 1) : $this->config['minCount'];
        
        // Extract and count tokens
        $tokens = $this->extractTokens($titles);
        
        // Extract bigrams if enabled
        $bigrams = [];
        if ($this->config['useBigrams']) {
            $bigrams = $this->extractNGrams($titles, 2);
        }
        
        // Extract trigrams if enabled
        $trigrams = [];
        if ($this->config['useTrigrams']) {
            $trigrams = $this->extractNGrams($titles, 3);
        }
        
        // Combine and prioritize
        $result = [];
        
        if ($this->config['phraseFirst']) {
            // Prioritize phrases over single words
            $result = array_merge(
                $this->filterAndSort($trigrams, $minCount, $this->config['maxTrigrams']),
                $this->filterAndSort($bigrams, $minCount, $this->config['maxBigrams']),
                $this->filterAndSort($tokens, $minCount, $this->config['maxWords'])
            );
        } else {
            // Mix all together
            $combined = array_merge($tokens, $bigrams, $trigrams);
            $result = $this->filterAndSort($combined, $minCount, $this->config['maxWords']);
        }
        
        // Limit to maxWords
        return array_slice($result, 0, $this->config['maxWords']);
    }

    /**
     * Extract and count single word tokens
     */
    private function extractTokens(array $titles): array
    {
        $tokenCounts = [];
        
        foreach ($titles as $title) {
            $words = $this->tokenize($title);
            
            foreach ($words as $word) {
                // Apply filters
                if (!$this->isValidToken($word)) {
                    continue;
                }
                
                // Apply stemming
                if ($this->config['applyStemming']) {
                    $word = $this->stemmer->stem($word);
                }
                
                // Apply synonym normalization
                $word = $this->normalizeSynonym($word);
                
                // Count
                if (!isset($tokenCounts[$word])) {
                    $tokenCounts[$word] = 0;
                }
                $tokenCounts[$word]++;
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
            $words = $this->tokenize($title);
            
            // Filter words first
            $validWords = array_filter($words, function($word) {
                return $this->isValidToken($word);
            });
            
            // Reset array keys
            $validWords = array_values($validWords);
            
            // Extract n-grams
            for ($i = 0; $i <= count($validWords) - $n; $i++) {
                $ngram = array_slice($validWords, $i, $n);
                
                // Apply stemming to each word in n-gram
                if ($this->config['applyStemming']) {
                    $ngram = array_map(function($word) {
                        return $this->stemmer->stem($word);
                    }, $ngram);
                }
                
                // Apply synonym normalization
                $ngram = array_map(function($word) {
                    return $this->normalizeSynonym($word);
                }, $ngram);
                
                $ngramStr = implode(' ', $ngram);
                
                // Count
                if (!isset($ngramCounts[$ngramStr])) {
                    $ngramCounts[$ngramStr] = 0;
                }
                $ngramCounts[$ngramStr]++;
            }
        }
        
        return $ngramCounts;
    }

    /**
     * Tokenize text into words
     */
    private function tokenize(string $text): array
    {
        // Convert to lowercase
        $text = mb_strtolower($text, 'UTF-8');
        
        // Remove special characters, keep only letters, numbers, and spaces
        $text = preg_replace('/[^a-z0-9\s]/u', ' ', $text);
        
        // Split into words
        $words = preg_split('/\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);
        
        return $words;
    }

    /**
     * Check if token is valid
     */
    private function isValidToken(string $token): bool
    {
        // Length filter
        if (mb_strlen($token, 'UTF-8') < $this->config['minTokenLength']) {
            return false;
        }
        
        // Stopwords filter
        if (in_array($token, $this->stopwords)) {
            return false;
        }
        
        // Blocklist filter
        if (in_array($token, $this->blocklist)) {
            return false;
        }
        
        // Must contain at least one letter
        if (!preg_match('/[a-z]/i', $token)) {
            return false;
        }
        
        // Heuristic: filter potential proper nouns (all caps or title case in original)
        // This is a simple heuristic and may not be perfect
        
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
    private function filterAndSort(array $counts, int $minCount, int $limit): array
    {
        // Filter by minimum count
        $filtered = array_filter($counts, function($count) use ($minCount) {
            return $count >= $minCount;
        });
        
        // Remove single occurrences if configured
        if ($this->config['removeSingleOccurrence']) {
            $filtered = array_filter($filtered, function($count) {
                return $count > 1;
            });
        }
        
        // Sort by count descending
        arsort($filtered);
        
        // Convert to array of objects for frontend
        $result = [];
        foreach ($filtered as $text => $count) {
            $result[] = [
                'text' => $text,
                'value' => $count
            ];
        }
        
        return array_slice($result, 0, $limit);
    }

    /**
     * Add custom stopwords
     */
    public function addStopwords(array $words): void
    {
        $this->stopwords = array_merge($this->stopwords, $words);
    }

    /**
     * Add custom blocklist items
     */
    public function addBlocklist(array $items): void
    {
        $this->blocklist = array_merge($this->blocklist, $items);
    }

    /**
     * Add custom synonyms
     */
    public function addSynonyms(array $synonyms): void
    {
        $this->synonymMap = array_merge($this->synonymMap, $synonyms);
    }
}
