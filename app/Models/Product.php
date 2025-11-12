<?php

namespace App\Models;

use App\Enums\ProductStock;
use App\Enums\ProductType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    use HasFactory;

    public const PAGINATION_PER_PAGE = 12;

    public const DEFAULT_STATUS = ProductType::ACTIVE;

    public const IMAGE_STORAGE_PATH = 'products';

    public const IMAGE_DISK = 'public';

    public const MIN_STOCK_ALERT = 20;

    protected $fillable = [
        'name',
        'category_id',
        'description',
        'price',
        'stock',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => ProductType::class,
            'price'  => 'decimal:2',
            'stock'  => 'integer',

        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function mainImage(): HasOne
    {
        return $this->hasOne(ProductImage::class)->where('is_main', true);
    }

    public function attributes(): HasMany
    {
        return $this->hasMany(Attribute::class);
    }

    /**
     * Scope cho sản phẩm active
     */
    public function scopeActive($query): mixed
    {
        return $query->where('status', ProductType::ACTIVE);
    }

    /**
     * Scope cho sản phẩm có stock
     */
    public function scopeInStock($query): mixed
    {
        return $query->where('stock', '>', 0);
    }

    /**
     * Lấy hình ảnh chính
     */
    public function getMainImageAttribute(): mixed
    {
        return $this->images->where('is_main', true)->first() ?? $this->images->first();
    }

    /**
     * Format price với VND
     */
    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price, 0, ',', '.') . ' ₫';
    }

    /**
     * Lấy các attributes dưới dạng array
     */
    public function getAttributesArrayAttribute(): mixed
    {
        return $this->attributes->mapWithKeys(function ($attribute) {
            return [
                $attribute->name => $attribute->values->pluck('value')->toArray(),
            ];
        });
    }

    public function scopeWithRelations($query): mixed
    {
        return $query->with(['category', 'images', 'attributes.values']);
    }

    /**
     * Lọc sản phẩm
     *
     * @param mixed $query
     * @param array $filters
     * @return void
     */
    public function scopeFilter($query, array $filters): void
    {
        $query->when($filters['status'] ?? null, fn ($q, $status) => $q->where('status', $status)
        );

        $query->when($filters['category_id'] ?? null, fn ($q, $category) => $q->where('category_id', $category)
        );

        $query->when($filters['min_price'] ?? null, fn ($q, $min) => $q->where('price', '>=', $min)
        );

        $query->when($filters['max_price'] ?? null, fn ($q, $max) => $q->where('price', '<=', $max)
        );

        $query->when($filters['search'] ?? null, fn ($q, $search) => $q->where('name', 'like', "%{$search}%")
        );
    }

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
        return $this->stock > 0 && $this->stock <= self::MIN_STOCK_ALERT;
    }

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
