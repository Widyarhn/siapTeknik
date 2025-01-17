<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubKriteria extends Model
{
    use HasFactory;

    protected $table = 'sub_kriterias';
    protected $guarded = [];

    public function kriteria()
    {
        return $this->belongsTo(Kriteria::class);
    }
    public function indikator()
    {
        return $this->hasMany('App\Models\Indikator');
    }
    public function rumus()
    {
        return $this->hasOne(Rumus::class);
    }
}
