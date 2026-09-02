<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InputData extends Model
{
    protected $table = 'input_data';

    protected $fillable = [
        'nama',
        'email',
        'no_hp',
        'alamat',
        'provinsi',
        'kota',
        'kecamatan',
        'kelurahan',
        'level',
        'range_gaji',
        'gaji',
    ];

    protected $casts = [
        'gaji' => 'integer',
    ];
}