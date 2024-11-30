<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SoalTryout extends Model
{
    use HasFactory;
    protected $keyType = 'string';
    public $incrementing = false;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = (string) Str::uuid();
        });
    }

    protected $fillable = [
        'id',
        'bidang_id',
        'kompetensi_id',
        'nomor',
        'tryout_id',
        'soal',
        'pilihan_a',
        'pilihan_b',
        'pilihan_c',
        'pilihan_d',
        'pilihan_e',
        'jawaban',
    ];

    public function tryout()
    {
        return $this->belongsTo(Tryouts::class);
    }

    public function bidang()
    {
        return $this->belongsTo(BidangTryouts::class);
    }

    public function kompetensi()
    {
        return $this->belongsTo(KompetensiTryouts::class);
    }

    public function userAnswer()
    {
        return $this->hasOne(UserAnswer::class, 'soal_id', 'id');
    }
}
