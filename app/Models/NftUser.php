<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NftUser extends Model
{
    protected $table = 'nft_user';
    protected $fillable = [
        'user_id',
        'nft_id',
        'balance',
    ];
}
