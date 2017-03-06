<?php

namespace Exitialis\Mas;

use Illuminate\Database\Eloquent\Model;

class MasKey extends Model
{
    /**
     * Разрешено массовое заполнение всех полей.
     *
     * @var array
     */
    public $guarded = [];

    /**
     * Имеет пользователя.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Получить по сессии.
     *
     * @param $query
     * @param $session
     * @return mixed
     */
    public function scopeBySession($query, $session)
    {
        return $query->where("session", $session);
    }

    /**
     * Получить по hash пользователя.
     *
     * @param $query
     * @param $user_hash
     * @return mixed
     */
    public function scopeByUserHash($query, $user_hash)
    {
        return $query->where("user_hash",  $user_hash)->first();
    }
}
