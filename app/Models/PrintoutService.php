<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrintoutService extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'size',
        'side',
        'pages',
        'color',
        'price',
        'service_charge',
    ];
}