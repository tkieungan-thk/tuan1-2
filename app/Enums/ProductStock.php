<?php

namespace App\Enums;

enum ProductStock: int
{
    case IN_STOCK     = 1;
    case OUT_OF_STOCK = 0;

    public function label(): string
    {
        return match ($this) {
            self::IN_STOCK     => __('products.stock_enum.in_stock'),
            self::OUT_OF_STOCK => __('products.stock_enum.out_of_stock'),
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::IN_STOCK     => 'success',
            self::OUT_OF_STOCK => 'danger',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::IN_STOCK     => 'fa-check',
            self::OUT_OF_STOCK => 'fa-times',
        };
    }

    public static function fromStock(int $stock): self
    {
        return $stock > 0 ? self::IN_STOCK : self::OUT_OF_STOCK;
    }
}
