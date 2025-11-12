<?php

namespace App\Enums;

enum ProductType: string
{
    case ACTIVE   = 'active';
    case INACTIVE = 'inactive';
    case DRAFT    = 'draft';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function labels(): array
    {
        return [
            self::ACTIVE->value   => __('products.status_enum.active'),
            self::INACTIVE->value => __('products.status_enum.inactive'),
            self::DRAFT->value    => __('products.status_enum.draft'),
        ];
    }

    public function label(): string
    {
        return self::labels()[$this->value] ?? $this->value;
    }

    public function color(): string
    {
        return match ($this) {
            self::ACTIVE   => 'success',
            self::INACTIVE => 'danger',
            self::DRAFT    => 'warning',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::ACTIVE   => 'fa-check-circle',
            self::INACTIVE => 'fa-times-circle',
            self::DRAFT    => 'fa-edit',
        };
    }

    public function isActive(): bool
    {
        return $this === self::ACTIVE;
    }

    public function isDraft(): bool
    {
        return $this === self::DRAFT;
    }
}
