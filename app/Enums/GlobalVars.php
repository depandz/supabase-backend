<?php

namespace App\Enums;

class GlobalVars
{
    public static $CLIENT_SID_IDENTIFICATION = 'cls';

    public static $DRIVER_SID_IDENTIFICATION = 'drs';

    public static $PICKUP_SID_IDENTIFICATION = 'pic';

    public static function getDefaultProfilePicture(string $full_name){
        return "https://ui-avatars.com/api/?name=$full_name&background=0D8ABC&color=fff";
    }

}
