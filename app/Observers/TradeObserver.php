<?php

namespace App\Observers;

use App\Models\Trade;
use App\Events\NewTradeEvent;

class TradeObserver
{


    /**
     * Handle the Trade "created" event.
     */
    public function created(Trade $trade): void
    {

        NewTradeEvent::dispatch($trade);
        // Update current period candles
    }
}
