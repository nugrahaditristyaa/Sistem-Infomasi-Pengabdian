<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class UniqueNimsInArray implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute Nama field yang divalidasi ('mahasiswa_baru')
     * @param  mixed  $value Nilai dari field tersebut (seluruh array mahasiswa_baru)
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Jika value bukan array atau kosong, loloskan saja.
        if (!is_array($value) || empty($value)) {
            return true;
        }

        // 1. Ambil semua NIM dari array input ke dalam satu array baru.
        $nims = array_column($value, 'nim');

        // 2. Hilangkan NIM yang kosong atau null agar tidak dihitung sebagai duplikat.
        $filteredNims = array_filter($nims);

        // 3. Aturan GAGAL jika jumlah NIM setelah difilter tidak sama dengan
        //    jumlah NIM unik setelah difilter. Ini menandakan ada duplikasi.
        return count($filteredNims) === count(array_unique($filteredNims));
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        // Ini adalah satu-satunya pesan yang akan ditampilkan jika ada duplikasi.
        return 'Terdapat duplikasi NIM pada baris mahasiswa baru yang Anda tambahkan.';
    }
}

