<?php

namespace App\Enums;

enum VehicleTypes:string
{
    case LIGHT = 'light';

    case HEAVY = 'heavy';

    case TRUCK = 'truck';
    public static function toArray(): array
    {
        $array = [];
        foreach (self::cases() as $case) {
            $array[$case->value] = $case->name;
        }
        return $array;
    }
}
