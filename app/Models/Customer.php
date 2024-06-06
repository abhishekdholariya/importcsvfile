<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    protected $fillable=[
        'customer_id',
        'fname',
        'lname',
        'company',
        'city',
        'country',
        'phone_first',
        'phone_second',
        'email',
        'subscription_date',
        'website'
    ];
}
