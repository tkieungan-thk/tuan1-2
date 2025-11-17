<?php

namespace App\Enums;

enum ProductStock: int
{
    case IN_STOCK     = 1;
    case OUT_OF_STOCK = 0;

    /**
     * Lấy tên hiển thị tương ứng với giá trị enum.
     *
     * @return array|string|null
     */
    public function label(): string
    {
        return match ($this) {
            self::IN_STOCK     => __('products.stock_enum.in_stock'),
            self::OUT_OF_STOCK => __('products.stock_enum.out_of_stock'),
        };
    }

    /**
     * Lấy màu tương ứng với giá trị enum.
     *
     * @return string
     */
    public function color(): string
    {
        return match ($this) {
            self::IN_STOCK     => 'success',
            self::OUT_OF_STOCK => 'danger',
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
            self::IN_STOCK     => 'fa-check',
            self::OUT_OF_STOCK => 'fa-times',
        };
    }

    /**
     * xác định trạng thái tồn kho dựa trên số lượng tồn.
     *
     * @param int $stock
     * @return ProductStock
     */
    public static function fromStock(int $stock): self
    {
        return $stock > 0 ? self::IN_STOCK : self::OUT_OF_STOCK;
    }
}
