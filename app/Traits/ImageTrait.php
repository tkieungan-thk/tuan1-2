<?php

namespace App\Traits;

use App\Models\Product;

trait ImageTrait
{
    /**
     * Lấy hình ảnh chính
     *
     * @return mixed
     */
    public function getMainImageAttribute(): mixed
    {
        return $this->images->where('is_main', true)->first() ?? $this->images->first();
    }

    /**
     * Lấy storage path cho images
     *
     * @return string
     */
    public function getImageStoragePath(): string
    {
        return defined('self::IMAGE_STORAGE_PATH')
            ? Product::IMAGE_STORAGE_PATH
            : strtolower(class_basename($this)) . 's';
    }

    /**
     * Lấy image disk
     *
     * @return string
     */
    public function getImageDisk(): string
    {
        return defined('self::IMAGE_DISK') ? Product::IMAGE_DISK : 'public';
    }
}
