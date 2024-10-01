<?php

namespace App\Enums;

enum TableStatus: string
{
    case Pending = 'pending';
    case Awailable = 'awailable';
    case Unavailable = 'unavailable';


}
