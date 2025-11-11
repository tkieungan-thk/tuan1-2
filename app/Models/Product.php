<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category_id',
        'description',
        'price',
        'stock',
        'status',
    ];

    protected $casts = [
        'price'  => 'decimal:2',
        'stock'  => 'integer',
        'status' => 'string',
    ];

    const STATUS_ACTIVE = 'active';

    const STATUS_INACTIVE = 'inactive';

    const STATUS_DRAFT = 'draft';

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
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope cho sản phẩm có stock
     */
    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    /**
     * Lấy hình ảnh chính
     */
    public function getMainImageAttribute()
    {
        return $this->images->where('is_main', true)->first() ?? $this->images->first();
    }

    /**
     * Format price với VND
     */
    public function getFormattedPriceAttribute()
    {
        return number_format($this->price, 0, ',', '.') . ' ₫';
    }

    /**
     * Lấy các attributes dưới dạng array
     */
    public function getAttributesArrayAttribute()
    {
        return $this->attributes->mapWithKeys(function ($attribute) {
            return [
                $attribute->name => $attribute->values->pluck('value')->toArray(),
            ];
        });
    }
}
