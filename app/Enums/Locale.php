<?php

namespace App\Enums;

enum Locale: string
{
    case EN = 'en';
    case VI = 'vi';

    /**
     * Lấy tên hiển thị tương ứng với giá trị enum.
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::EN => __('layout.en'),
            self::VI => __('layout.vi'),
        };
    }

    /**
     * Lấy danh sách các giá trị của enum.
     *
     * @return array<int, string> Danh sách value của các enum case
     */
    public static function all(): array
    {
        return array_column(self::cases(), 'value');
    }
}
