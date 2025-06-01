<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\NftType;

class Nft extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'nfts';

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
            'abi' => 'array',
            'metadata' => 'array',
            'type' => NftType::class,
            'active' => 'boolean'
        ];
    }

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'chainId',
        'name',
        'symbol',
        'contract',
        'abi',
        'metadata',
        'image',
        'type',
        'active'
    ];

    /**
     * select only latest factories per chainId
     */
    public function scopeLatestByChain(Builder $query)
    {
        return $query->whereIn('id', function ($subquery) {
            $subquery->selectRaw('MAX(id)')
                ->groupBy('chainId');
        });
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'nft_user', 'nft_id', 'user_id')
            ->withPivot('balance');
    }

    /**

     * Get the uploads this model Belongs To.
     *
     */
    public function uploads(): MorphMany
    {
        return $this->morphMany(Upload::class, 'uploadable');
    }
}
