<?php

namespace App\Enums;

enum UserStatus: int
{
    case ACTIVE = 1;
    case LOCKED = 0;

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => __('users.status_enum.active'),
            self::LOCKED => __('users.status_enum.locked'),
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::ACTIVE => 'success',
            self::LOCKED => 'warning',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::ACTIVE => 'fa-unlock',
            self::LOCKED => 'fa-lock',
        };
    }
}
