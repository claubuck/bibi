<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_date',
        'product_name',
        'product_category',
        'unit_price',
        'quantity',
        'total_sale',
        'customer_type',
        'payment_method',
        'region',
        'sales_person'
    ];

    protected $casts = [
        'sale_date' => 'date'
    ];

    // Accesor para total calculado
    public function getCalculatedTotalAttribute()
    {
        return $this->quantity * $this->unit_price;
    }
}
