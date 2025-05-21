<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'total_amount',
        'discount_amount',
        'final_amount',
        'payment_method',
        'status'
    ];

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function calculateDiscounts()
    {
        $totalDiscount = 0;

        foreach ($this->orderDetails as $detail) {
            $discount = $detail->product->discounts()
                ->active()
                ->first();

            if ($discount) {
                $discountAmount = ($detail->price * $detail->quantity) * ($discount->percentage / 100);
                $detail->discount = $discountAmount;
                $detail->save();
                $totalDiscount += $discountAmount;
            }
        }

        $this->discount_amount = $totalDiscount;
        $this->final_amount = $this->total_amount - $totalDiscount;
        $this->save();
    }
}