<?php

namespace App\Traits;

use App\Enums\ProductStock;
use App\Models\Product;

trait StockTrait
{
    /**
     * Lấy trạng thái tồn kho
     *
     * @return ProductStock
     */
    public function getStockStatusAttribute(): ProductStock
    {
        return ProductStock::fromStock($this->stock);
    }

    /**
     * Kiểm tra sản phẩm còn hàng
     *
     * @return bool
     */
    public function isInStock(): bool
    {
        return $this->stock > 0;
    }

    /**
     * Kiểm tra sản phẩm hết hàng
     *
     * @return bool
     */
    public function isOutOfStock(): bool
    {
        return $this->stock <= 0;
    }

    /**
     * Sản phẩm còn hàng số lượng thấp
     *
     * @return bool
     */
    public function isLowStock(): bool
    {
        return $this->stock > 0 && $this->stock <= Product::MIN_STOCK_ALERT;
    }

    /**
     * Lấy nội dung stock
     *
     * @return string
     */
    public function getFormattedStockAttribute(): string
    {
        if ($this->isOutOfStock()) {
            return $this->stockStatus->label();
        }

        return $this->isLowStock()
            ? __('products.stock_enum.low_stock', ['quantity' => $this->stock])
            : __('products.stock_enum.in_stock_with_quantity', ['quantity' => $this->stock]);
    }
}
