<?php

namespace App\Enums;

enum UserStatus: int
{
    case ACTIVE = 1;
    case LOCKED = 0;

    /**
     * Lấy tên hiển thị tương ứng với giá trị enum.
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => __('users.status_enum.active'),
            self::LOCKED => __('users.status_enum.locked'),
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
            self::ACTIVE => 'success',
            self::LOCKED => 'warning',
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
            self::ACTIVE => 'fa-unlock',
            self::LOCKED => 'fa-lock',
        };
    }

    /**
     * Tạo thẻ span hiển thị trạng thái với màu và tên hiển thị tương ứng.
     *
     * @return string
     */
    public function badge(): string
    {
        return "<span class=\"badge badge-{$this->color()}\"> {$this->label()}</span>";
    }
}
