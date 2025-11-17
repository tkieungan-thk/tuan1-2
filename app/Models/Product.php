<?php

namespace App\Models;

use App\Enums\ProductType;
use App\Helpers\PriceHelper;
use App\Traits\ImageTrait;
use App\Traits\SearcchTrait;
use App\Traits\StockTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    use HasFactory, ImageTrait, SearcchTrait, StockTrait;

    public const PAGINATION_PER_PAGE = 12;

    public const DEFAULT_STATUS = ProductType::ACTIVE;

    public const IMAGE_STORAGE_PATH = 'products';

    public const IMAGE_DISK = 'public';

    public const MIN_STOCK_ALERT = 20;

    protected array $searchable = ['name', 'description'];

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
     * Format price với VND
     */
    public function getFormattedPriceAttribute(): string
    {
        return PriceHelper::formatVND($this->price);
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
}
