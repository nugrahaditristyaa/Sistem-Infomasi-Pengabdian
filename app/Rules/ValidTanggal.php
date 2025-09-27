<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Carbon\Carbon;

class ValidTanggal implements Rule
{
    private $minYear;
    private $fieldName;
    private $errorMessage; // Properti baru untuk menyimpan pesan error spesifik

    public function __construct($minYear = 2000, $fieldName = 'Tanggal')
    {
        $this->minYear = $minYear;
        $this->fieldName = $fieldName;
    }

    public function passes($attribute, $value)
    {
        try {
            $date = Carbon::createFromFormat('Y-m-d', $value);
            
            if ($date->format('Y-m-d') !== $value) {
                // 1. Gagal karena tanggal tidak ada di kalender (misal: 30 Februari)
                $this->errorMessage = "{$this->fieldName} yang dimasukkan bukan tanggal yang valid.";
                return false;
            }
        } catch (\Exception $e) {
            // 2. Gagal karena formatnya salah (misal: "abcde")
            $this->errorMessage = "{$this->fieldName} memiliki format yang tidak valid.";
            return false;
        }

        // 3. Gagal karena tahun terlalu lampau
        if ($date->year < $this->minYear) {
            $this->errorMessage = "{$this->fieldName} tidak boleh lebih awal dari tahun {$this->minYear}.";
            return false;
        }

        // 4. Gagal karena tanggal di masa depan
        if ($date->isFuture()) {
            $this->errorMessage = "{$this->fieldName} tidak boleh tanggal di masa depan.";
            return false;
        }

        return true; // Jika semua pengecekan lolos
    }

    public function message()
    {
        // Kembalikan pesan error spesifik yang sudah kita simpan
        return $this->errorMessage;
    }
}