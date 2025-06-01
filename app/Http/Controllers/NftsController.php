<?php

namespace App\Http\Controllers;

use App\Enums\NftType;
use App\Http\Controllers\Controller;
use App\Http\Resources\Nft as ResourcesNft;
use App\Models\Nft;
use App\Models\NftUser;
use App\Services\Web3NftChecker;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NftsController extends Controller
{

    /**
     * view the kyc page.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $kycNfts = Nft::where('type', NftType::VERIFICATION)
            ->latestByChain()
            ->where('active', true)
            ->get();
        $planktonNfts = Nft::where('type', NftType::PLANKTON)
            ->latestByChain()
            ->where('active', true)
            ->get();
        $fishNfts = Nft::where('type', NftType::FISH)
            ->latestByChain()
            ->where('active', true)
            ->get();
        $piranhaNfts = Nft::where('type', NftType::PIRANHA)
            ->latestByChain()
            ->where('active', true)
            ->get();
        $barracudaNfts = Nft::where('type', NftType::BARRACUDA)
            ->latestByChain()
            ->where('active', true)
            ->get();
        $sharkNfts = Nft::where('type', NftType::SHARK)
            ->latestByChain()
            ->where('active', true)
            ->get();
        $whaleNfts = Nft::where('type', NftType::WHALE)
            ->latestByChain()
            ->where('active', true)
            ->get();
        $user = $request->user();
        $balances = NftUser::where('user_id', $user->id)->pluck('balance', 'nft_id');
        return Inertia::render('Nfts/Mint', [
            'kycNfts' => ResourcesNft::collection($kycNfts)->keyBy('chainId'),
            'planktonNfts' => ResourcesNft::collection($planktonNfts)->keyBy('chainId'),
            'fishNfts' => ResourcesNft::collection($fishNfts)->keyBy('chainId'),
            'piranhaNfts' => ResourcesNft::collection($piranhaNfts)->keyBy('chainId'),
            'barracudaNfts' => ResourcesNft::collection($barracudaNfts)->keyBy('chainId'),
            'sharkNfts' => ResourcesNft::collection($sharkNfts)->keyBy('chainId'),
            'whaleNfts' => ResourcesNft::collection($whaleNfts)->keyBy('chainId'),
            'balances' => $balances
        ]);
    }

    /**
     * Display the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function meta(Request $request, Nft $nft)
    {
        return $nft->metadata;
    }


    public function verify(Request $request, Nft $nft)
    {
        $user = $request->user();
        $nftChecker = new Web3NftChecker($nft);
        // Check if user has any NFTs
        if ($nft->type === NftType::VERIFICATION) {
            $user->verified_address = $nftChecker->hasNft($request->user());
            $user->save();
        }
        $balance = $nftChecker->getBalance($request->user());
        if ($balance > 0) {
            $user->nfts()->attach($nft->id, ['balance' => $balance]);
        }
        return back();
    }
}
