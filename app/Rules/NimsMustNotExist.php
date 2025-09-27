<?php

namespace App\Rules;

use App\Models\Mahasiswa; // <-- Jangan lupa import model Mahasiswa
use Illuminate\Contracts\Validation\Rule;

class NimsMustNotExist implements Rule
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
        // 1. Ambil semua NIM dari array input, abaikan yang kosong.
        $nims = array_filter(array_column($value, 'nim'));

        // 2. Jika tidak ada NIM yang diinput, maka validasi dianggap lolos.
        if (empty($nims)) {
            return true;
        }

        // 3. Cek ke database apakah ada salah satu NIM yang sudah ada.
        //    Ini hanya butuh 1 query untuk memeriksa semua NIM.
        $exists = Mahasiswa::whereIn('nim', $nims)->exists();

        // 4. Validasi akan lolos jika tidak ada (exists = false) NIM yang ditemukan.
        return !$exists;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'NIM Mahasiswa Baru yang Anda masukkan sudah terdaftar.';
    }
}