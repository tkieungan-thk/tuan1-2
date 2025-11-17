<?php

namespace App\Enums;

enum ProductType: string
{
    case ACTIVE   = 'active';
    case INACTIVE = 'inactive';
    case DRAFT    = 'draft';

    /**
     * Lấy danh sách giá trị của enum
     *
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Lấy danh sách hiển thị tương ứng với giá trị enum.
     *
     * @return array
     */
    public static function labels(): array
    {
        return [
            self::ACTIVE->value   => __('products.status_enum.active'),
            self::INACTIVE->value => __('products.status_enum.inactive'),
            self::DRAFT->value    => __('products.status_enum.draft'),
        ];
    }

    /**
     *Lấy tên hiển thị của enum hiện tại.
     *
     * @return string
     */
    public function label(): string
    {
        return self::labels()[$this->value] ?? $this->value;
    }

    /**
     * Lấy màu tương ứng với giá trị enum.
     *
     * @return string
     */
    public function color(): string
    {
        return match ($this) {
            self::ACTIVE   => 'success',
            self::INACTIVE => 'danger',
            self::DRAFT    => 'warning',
        };
    }

    /**
     * Lấy icon tương ứng với giá trị enum.
     *
     * @return string
     */
    public function icon(): string
    {
        return match ($this) {
            self::ACTIVE   => 'fa-check-circle',
            self::INACTIVE => 'fa-times-circle',
            self::DRAFT    => 'fa-edit',
        };
    }
}
