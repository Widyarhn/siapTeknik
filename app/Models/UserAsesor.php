<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAsesor extends Model
{
    use HasFactory;

    protected $table = 'user_asesors';
    protected $fillable = [
        'user_id',
        'program_studi_id',
        'tahun_id',
        'jenjang_id',
        'timeline_id',
        'jabatan'
    ];

    public function program_studi()
    {
        return $this->belongsTo(ProgramStudi::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function jenjang()
    {
        return $this->belongsTo(Jenjang::class);
    }

    public function tahun()
    {
        return $this->belongsTo(Tahun::class);
    }

    public function timeline()
    {
        return $this->belongsTo(Timeline::class);
    }

}