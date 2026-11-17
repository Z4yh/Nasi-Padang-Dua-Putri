<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CmsBeranda extends Model
{
    use HasFactory;

    protected $table = 'tigaputri_md_cms_beranda';
    protected $primaryKey = 'id_cms_beranda';

    protected $fillable = [
        'hero_title',
        'foto_warung',
        'jam_buka',
        'no_whatsapp_reservasi',
    ];
}
