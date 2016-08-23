<?php

namespace Exitialis\Mas;

use Illuminate\Database\Eloquent\Model;

class MasKey extends Model
{
    /**
     * Имеет пользователя.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(config('mas.users.model'));
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
