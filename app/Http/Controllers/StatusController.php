<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Nft;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StatusController extends Controller
{
    public function index()
    {
        // Total number of registered users
        $totalUsers = User::count();
        
        // Total number of verified users (who have email_verified_at set)
        $verifiedUsers = User::whereNotNull('email_verified_at')->count();
        
        // Total number of users who have minted an NFT (Premium Buyers)
        // Users who have NFT balance > 0
        $premiumBuyers = User::whereHas('nfts', function ($query) {
            $query->where('nft_user.balance', '>', 0);
        })->count();
        
        return Inertia::render('Status', [
            'stats' => [
                'totalUsers' => $totalUsers,
                'verifiedUsers' => $verifiedUsers,
                'premiumBuyers' => $premiumBuyers,
            ]
        ]);
    }
}
