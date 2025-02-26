<?php

namespace App\Http\Controllers;

use App\Enums\TradeType;
use App\Http\Controllers\Controller;
use App\Models\Trade;
use Illuminate\Http\Request;
use App\Models\Launchpad;
use App\Models\Rate;
use App\Services\TokenHolderService;
use Carbon\Carbon;


use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Enum;
use Inertia\Inertia;

class TradesController extends Controller
{



    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $request->validate([
            'launchpad_id' => ['required', 'integer', 'exists:launchpads,id'],
            'address' => ['required', 'string'],
            'qty' => ['required', 'string'],
            'txid' => ['required', 'string'],
            'amount' => ['required', 'string'],
            'type' => ['required', 'string', 'max:255', new Enum(TradeType::class)],
        ]);
        $trade = Trade::where('txid', $request->txid)->firstOrNew();
        $user = $request->user();
        //if user in not current silently discard
        if (strtolower($user->address) != strtolower($request->address)) return back();
        $trade->launchpad_id = $request->launchpad_id;
        $trade->txid = $request->txid;
        $trade->address = $request->address;
        $trade->qty = $request->qty;
        $trade->amount = $request->amount;
        $trade->price = bcdiv($request->amount, $request->qty, 16);
        $trade->usd_price = 0;
        $trade->type = $request->type;
        $trade->save();
        $launchpad = $trade->launchpad()->first();
        $rate = Rate::where('chainId', $launchpad->chainId)->first();
        if ($rate) {
            $trade->usd = $request->amount * $rate->usd_rate;
            $trade->usd_price =  bcdiv($trade->usd, $request->qty, 16);
            $trade->save();
        }
        app(TokenHolderService::class)->updateHolders($launchpad);
        return back();
    }


    /**
     * Get OHLCV candles for TradingView chart
     */
    public function getCandles(Request $request, Launchpad $launchpad)
    {
        $request->validate([
            'timeframe' => 'required|string',
            'from' => 'required|integer',
            'to' => 'required|integer',
        ]);
        $from = Carbon::createFromTimestamp($request->from);
        $to = Carbon::createFromTimestamp($request->to);
        $timeframeSeconds = match ($request->timeframe) {
            '1' => 60,
            '5' => 300,
            '15' => 900,
            '30' => 1800,
            '60' => 3600,
            '240' => 14400,
            'D' => 86400,
            'W' => 604800
        };
        return DB::table('trades')
            ->select([
                DB::raw("FLOOR(UNIX_TIMESTAMP(created_at) / $timeframeSeconds) * $timeframeSeconds as timestamp"),
                DB::raw('MIN(price) as low'),
                DB::raw('MAX(price) as high'),
                DB::raw('SUBSTRING_INDEX(GROUP_CONCAT(price ORDER BY created_at ASC), ",", 1) as open'),
                DB::raw('SUBSTRING_INDEX(GROUP_CONCAT(price ORDER BY created_at DESC), ",", 1) as close'),
                DB::raw('SUM(qty) as volume'),
                DB::raw('COUNT(*) as trades_count')
            ])
            ->where('launchpad_id', $launchpad->id)
            ->whereBetween('created_at', [$from, $to])
            ->groupBy(DB::raw("FLOOR(UNIX_TIMESTAMP(created_at) / $timeframeSeconds) * $timeframeSeconds"))
            ->get();
    }




    public function leaderboard(Request $request, $type = 'volume', $period = '1M')
    {
        // Validate the type parameter
        $validTypes = ['volume', 'buyers', 'sellers', 'prebond', 'profits', 'trades', 'launchpads'];
        if ($type && !in_array($type, $validTypes)) {
            return back()->with('error', 'Invalid leaderboard type');
        }

        $query = Trade::query()
            ->select('address');

        switch ($type) {
            case 'buyers':
                $query->addSelect([
                    'address',
                    DB::raw('SUM(CASE WHEN type = ? THEN usd ELSE 0 END) as total_purchased'),
                    DB::raw('COUNT(CASE WHEN type = ? THEN 1 ELSE 0 END) as buy_count')
                ])
                    ->setBindings([TradeType::BUY->value, TradeType::BUY->value])
                    ->orderBy('total_purchased', 'desc');
                break;

            case 'sellers':
                $query->addSelect([
                    'address',
                    DB::raw('SUM(CASE WHEN type = ? THEN usd ELSE 0 END) as total_sold'),
                    DB::raw('COUNT(CASE WHEN type = ? THEN 1 ELSE 0 END) as sell_count')
                ])
                    ->setBindings([TradeType::SELL->value, TradeType::SELL->value])
                    ->orderBy('total_sold', 'desc');
                break;

            case 'prebond':
                $query->addSelect([
                    'address',
                    DB::raw('SUM(CASE WHEN type = ? THEN usd ELSE 0 END) as total_prebond'),
                    DB::raw('COUNT(CASE WHEN type = ? THEN 1 ELSE 0 END) as prebond_count')
                ])
                    ->setBindings([TradeType::PREBOND->value, TradeType::PREBOND->value])
                    ->orderBy('total_prebond', 'desc');
                break;

            case 'profits':
                $query->addSelect([
                    'address',
                    DB::raw('SUM(CASE WHEN type = ? THEN usd ELSE -usd END) as total_profit'),
                    DB::raw('COUNT(*) as trade_count')
                ])
                    ->setBindings([TradeType::SELL->value])
                    ->orderBy('total_profit', 'desc');
                break;

            case 'trades':
                $query->addSelect([
                    'address',
                    DB::raw('COUNT(*) as trade_count'),
                    DB::raw('SUM(usd) as total_volume')
                ])
                    ->orderBy('trade_count', 'desc');
                break;

            case 'launchpads':
                $query->addSelect([
                    'address',
                    DB::raw('COUNT(DISTINCT launchpad_id) as launchpad_count'),
                    DB::raw('COUNT(*) as trade_count'),
                    DB::raw('SUM(usd) as total_volume')
                ])
                    ->orderBy('launchpad_count', 'desc')
                    ->orderBy('trade_count', 'desc');
                break;

            default:
                $query->addSelect([
                    'address',
                    DB::raw('SUM(usd) as total_volume'),
                    DB::raw('COUNT(*) as trade_count')
                ])
                    ->orderBy('total_volume', 'desc');
        }

        $launchpad = null;
        // Add common filters
        if ($request->has('pool')) {
            $launchpad = Launchpad::query()->where('contract', $request->input('pool'))->first();
            if ($launchpad)
                $query->where('launchpad_id', $launchpad->id);
        }
        $timeframe = match ($period) {
            'D' => 86400,                 // 1 day
            'W' => 604800,                // 1 week
            '7D' => 604800,               // 7 days
            '2W' => 1209600,              // 2 weeks
            '1M' => 2592000,              // 1 month (30 days)
            '3M' => 7776000,              // 3 months (90 days)
            '6M' => 15552000,             // 6 months (180 days)
            '1Y' => 31536000              // 1 year (365 days)
        };
        $query->where('created_at', '>=', now()->subSeconds($timeframe));
        // Group by address and paginate
        $query->groupBy('address');
        $leads = $query->paginate($request->input('per_page', 50));
        return Inertia::render('Trades/Leaderboard', compact('leads', 'launchpad', 'type', 'period'));
    }




    public function project_leaderboard(Request $request, $type = null)
    {
        ### group and order trades by $trade->address ###
        ## these are types below.
        // buyers  , Order by sum amount usd  purchased  \App\Enums\TradeType::SELL
        // sellers ,Order by sum amount usd  sold  \App\Enums\TradeType::SELL
        // prebond ,Order by sum amount usd  purchased  \App\Enums\TradeType::PREBOND
        // profits , Order by sum usd  buys minus sum usd sells
        // trades , total amount of trades executed by the address
        // Launchpads, Most launchpads participated in by the address
    }
}
