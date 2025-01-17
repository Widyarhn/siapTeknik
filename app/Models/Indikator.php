<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Indikator extends Model
{
    use HasFactory;
    protected $table = 'indikators';
    protected $guarded = [];
    
    public function sub_kriteria()
    {
        return $this->belongsTo(SubKriteria::class);
    }

    public function matriks()
    {
        return $this->hasOne(MatriksPenilaian::class);
    }

    public function rumus()
    {
        return $this->hasOne(Rumus::class);
    }
}
