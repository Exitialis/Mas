<?php

namespace Exitialis\Mas;

use Illuminate\Database\Eloquent\Model;

class MasKeys extends Model
{
    protected $table = "mas_keys";

    public $timestamps = false;

    public function User()
    {
        return $this->belongsTo("App\User");
    }

    public function getUser($session, $uuid)
    {
        if (!preg_match("/^[a-zA-Z0-9_-]+$/", $uuid) || !preg_match("/^[a-zA-Z0-9:_-]+$/", $session))
            return null;
        return $this->where("session", "=", $session)->where("uuid", "=", $uuid)->first();
    }

    public function getUserBySelectedProfile($session, $user_hash)
    {
        if (!preg_match("/^[a-zA-Z0-9_-]+$/", $user_hash) || !preg_match("/^[a-zA-Z0-9:_-]+$/", $session))
            return null;
        return $this->where("session", "=", $session)->where("user_hash", "=", $user_hash)->first();
    }
}
