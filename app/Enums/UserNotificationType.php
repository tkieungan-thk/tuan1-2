<?php

namespace App\Enums;

enum UserNotificationType: string
{
    case CREATED = 'created';
    case UPDATED = 'updated';
}
