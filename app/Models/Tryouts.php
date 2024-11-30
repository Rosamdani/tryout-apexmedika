<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Tryouts extends Model
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
        'batch_id',
        'nama',
        'waktu',
        'tanggal',
        'status',
    ];

    public function questions(): HasMany
    {
        return $this->hasMany(SoalTryout::class, 'tryout_id');
    }

    public function batch()
    {
        return $this->belongsTo(BatchTryouts::class, 'batch_id');
    }
}
