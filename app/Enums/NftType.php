<?php

namespace App\Enums;

enum NftType: string
{
    case VERIFICATION = 'verification';
    case PLANKTON = 'plankton';
    case FISH = 'fish';
    case PIRANHA = 'piranha';
    case BARRACUDA = 'barracuda';
    case SHARK = 'shark';
    case WHALE = 'whale';

    public function label(): string
    {
        return match ($this) {
            self::VERIFICATION => 'Verified Checkmark',
            self::PLANKTON => 'Mighty Plankton',
            self::FISH => 'Speed Fish',
            self::PIRANHA => 'Piranha Swarm',
            self::BARRACUDA => 'Barracuda Boss',
            self::SHARK => 'Shark',
            self::WHALE => 'Whale Partner',
        };
    }
}
