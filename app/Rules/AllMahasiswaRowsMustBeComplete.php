<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class AllMahasiswaRowsMustBeComplete implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Loop melalui setiap baris mahasiswa yang diinput
        foreach ($value as $mahasiswa) {
            // Cek kondisi: JIKA NIM diisi, TAPI nama KOSONG ATAU prodi KOSONG
            if (!empty($mahasiswa['nim']) && (empty($mahasiswa['nama']) || empty($mahasiswa['prodi']))) {
                return false; // Langsung gagalkan validasi jika kondisi terpenuhi
            }
        }

        return true; // Lolos jika semua baris yang ada NIM-nya sudah lengkap
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Setiap baris mahasiswa yang diisi NIM-nya juga wajib diisi Nama dan Prodinya.';
    }
}