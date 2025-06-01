<?php

namespace App\Models;

use App\Enums\LaunchpadStatus;
use App\Enums\NftType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Launchpad extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'launchpads';

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
            'status' => LaunchpadStatus::class,
            'nft_type' => NftType::class,
            'featured' => 'boolean',
            'kingofthehill' => 'boolean',
            'active' => 'boolean'
        ];
    }

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'factory_id',
        'contract',
        'token',
        'pool',
        'graph',
        'name',
        'symbol',
        'nft_type',
        'description',
        'chainId',
        'twitter',
        'discord',
        'telegram',
        'website',
        'livestreamId',
        'status',
        'logo',
        'featured',
        'kingofthehill',
        'active'
    ];

    /**
     * Get the bots this lanchpad owns
     */
    public function bots(): HasMany
    {
        return $this->hasMany(Bot::class);
    }


    /**

     * Get the factory this model Belongs To.
     *
     */
    public function factory(): BelongsTo
    {
        return $this->belongsTo(Factory::class, 'factory_id', 'id');
    }

    /**

     * Get the user this model Belongs To.
     *
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**

     * Get the trades this model Owns.
     *
     */
    public function trades(): HasMany
    {
        return $this->hasMany(Trade::class, 'launchpad_id', 'id');
    }

    /**

     * Get the trades this model Owns.
     *
     */
    public function holders(): HasMany
    {
        return $this->hasMany(Holder::class, 'launchpad_id', 'id');
    }

    /**

     * Get the msgs this model Owns.
     *
     */
    public function msgs(): HasMany
    {
        return $this->hasMany(Msg::class, 'launchpad_id', 'id');
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
