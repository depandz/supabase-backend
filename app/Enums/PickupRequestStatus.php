<?php

namespace App\Enums;

class PickupRequestStatus
{
    public static $INITIALIZED = 0;
    public static $PENDING = 1;
    public static $APPROVED = 1;
    public static $CANCELED = 3;
    public static $VALIDATED = 4;
}
