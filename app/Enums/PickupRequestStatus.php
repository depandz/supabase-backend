<?php

namespace App\Enums;

enum  PickupRequestStatus:string
{
   case INITIALIZED = 'initialized';
   case PENDING =  'pending';
   case APPROVED = 'approved';
   case CANCELED = 'canceled';
   case VALIDATED = 'validated';
}
