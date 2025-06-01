<?php

namespace App\Http\Controllers;

use App\Enums\NftType;
use App\Http\Controllers\Controller;
use App\Http\Resources\Nft as ResourcesNft;
use App\Models\Nft;
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
    public function kyc(Request $request)
    {
        $nfts = Nft::latestByChain()->get();
        return Inertia::render('Nfts/Mint', [
            'nfts' => ResourcesNft::collection($nfts)->keyBy('chainId')
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
