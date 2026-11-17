<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettingKatering extends Model
{
    use HasFactory;

    protected $table = 'tigaputri_md_setting_katering';
    protected $primaryKey = 'id_setting_katering';

    protected $fillable = [
        'min_order_bonus',
        'jumlah_bonus_box',
        'min_dp_persen',
    ];

    protected $casts = [
        'min_order_bonus' => 'integer',
        'jumlah_bonus_box' => 'integer',
        'min_dp_persen' => 'integer',
    ];
}
