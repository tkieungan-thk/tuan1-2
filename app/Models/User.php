<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserStatus;
use App\Traits\FilterTrait;
use App\Traits\ResponseTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use FilterTrait, HasFactory, Notifiable, ResponseTrait;

    const STATUS_ENUM = UserStatus::class;

    protected array $searchable = ['name', 'email'];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'status'            => UserStatus::class,
        ];
    }

    /**
     * Lấy Badge (Enum) tương ứng với trạng thái người dùng.
     *
     * @return string
     */
    public function getBadgeAttribute(): string
    {
        return $this->status->badge();
    }

    /**
     *  Chuyển đổi trạng thái người dùng giữa ACTIVE và LOCKED.
     *
     * @return array|string|null
     */
    public function toggleStatus(): string
    {
        $this->status = $this->status === UserStatus::ACTIVE
            ? UserStatus::LOCKED
            : UserStatus::ACTIVE;

        $this->save();

        return $this->status === UserStatus::ACTIVE
            ? __('users.account_unlocked')
            : __('users.account_locked');
    }
}
