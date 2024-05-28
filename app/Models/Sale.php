<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $primaryKey = 'date';
    public $timestamps = false;

    protected $fillable = [
        'date',
        'net_value',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }
}
