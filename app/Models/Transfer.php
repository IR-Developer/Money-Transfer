<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    use HasFactory;

    protected $table = 'transfers';

    protected $fillable = [
        'src_id',
        'des_id',
        'status',
        'mount',
        'bank_ref_code',
        'our_ref_code',
    ];
}
