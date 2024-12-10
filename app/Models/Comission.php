<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comission extends Model
{
    use HasFactory;

    protected $table = "comissions";

    public function getCommission($amount) {
   
        return self::where('range_min', '<=', $amount)
               ->where(function ($query) use ($amount) {
                   $query->where('range_max', '>=', $amount)
                         ->orWhereNull('range_max');
               })
               ->first();
    
    }
}
