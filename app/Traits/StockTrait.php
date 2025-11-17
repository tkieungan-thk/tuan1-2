<?php

namespace App\Traits;

use App\Enums\ProductStock;
use Illuminate\Database\Eloquent\Builder;

trait StockTrait
{
    /**
     * Scope cho sản phẩm có stock
     */
    public function scopeInStock(Builder $query): Builder
    {
        return $query->where('stock', '>', 0);
    }

    /**
     * Scope cho sản phẩm sắp hết hàng
     */
    public function scopeLowStock(Builder $query): Builder
    {
        return $query->where('stock', '>', 0)
            ->where('stock', '<=', $this->getMinStockAlert());
    }

    /**
     * Scope cho sản phẩm hết hàng
     */
    public function scopeOutOfStock(Builder $query): Builder
    {
        return $query->where('stock', '<=', 0);
    }

    /**
     * Kiểm tra stock status
     */
    public function getStockStatusAttribute(): ProductStock
    {
        return ProductStock::fromStock($this->stock);
    }

    public function isInStock(): bool
    {
        return $this->stock > 0;
    }

    public function isOutOfStock(): bool
    {
        return $this->stock <= 0;
    }

    public function isLowStock(): bool
    {
        return $this->stock > 0 && $this->stock <= $this->getMinStockAlert();
    }

    /**
     * Format stock information
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
