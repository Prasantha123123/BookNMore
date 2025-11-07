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
        'totalprice'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($photocopyService) {
            $photocopyService->totalprice = $photocopyService->price + $photocopyService->service_charge;
        });

        static::updating(function ($photocopyService) {
            $photocopyService->totalprice = $photocopyService->price + $photocopyService->service_charge;
        });
    }

    /**
     * Calculate the total price
     *
     * @return float
     */
    public function calculateTotalPrice()
    {
        return $this->price + $this->service_charge;
    }
}