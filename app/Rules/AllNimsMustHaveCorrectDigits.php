<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class AllNimsMustHaveCorrectDigits implements Rule
{
    /**
     * Jumlah digit yang divalidasi.
     * @var int
     */
    private $digits = 8;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Constructor, bisa digunakan untuk kustomisasi di masa depan
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute Nama field yang divalidasi ('mahasiswa_baru')
     * @param  mixed  $value Nilai dari field tersebut (seluruh array mahasiswa_baru)
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Jika value bukan array atau kosong, loloskan saja validasinya.
        if (!is_array($value) || empty($value)) {
            return true;
        }

        // Loop melalui setiap item mahasiswa baru yang dikirimkan.
        foreach ($value as $item) {
            // Hanya validasi jika NIM diisi (tidak kosong).
            if (isset($item['nim']) && !empty($item['nim'])) {
                
                // Aturan GAGAL jika NIM tidak numerik ATAU panjangnya tidak sama dengan $this->digits.
                if (!is_numeric($item['nim']) || strlen((string)$item['nim']) !== $this->digits) {
                    return false; // Ditemukan satu NIM yang salah, langsung hentikan dan gagal.
                }
            }
        }

        return true; // Semua NIM yang diisi sudah valid.
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        // Ini adalah satu-satunya pesan yang akan ditampilkan jika validasi gagal.
        return "NIM harus diisi dengan {$this->digits} digit angka.";
    }
}

