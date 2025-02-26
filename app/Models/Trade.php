<?php

namespace App\Models;

use App\Enums\TradeType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trade extends Model
{
    use SoftDeletes, HasFactory;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'trades';

    /**
     * The database primary key value.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Attributes that should be cast to native types.
     *
     * @var string
     */
    protected function casts()
    {
        return [
            'type' => TradeType::class
        ];
    }

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'launchpad_id',
        'txid',
        'address',
        'qty',
        'amount',
        'price',
        'usd_price',
        'usd',
        'type'
    ];


    /**

     * Get the launchpad this model Belongs To.
     *
     */
    public function launchpad(): BelongsTo
    {
        return $this->belongsTo(Launchpad::class, 'launchpad_id', 'id');
    }
}
