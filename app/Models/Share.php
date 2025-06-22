<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Share extends Model
{
    protected $fillable = [
        'share_id', 'type', 'content', 'password', 'expires_at', 'creator_ip'
    ];

    protected $casts = [
        'expires_at' => 'datetime'
    ];

    public function files()
    {
        return $this->hasMany(ShareFile::class);
    }

    public function isExpired()
    {
        if ($this->expires_at === null) return false;
        return now()->greaterThan($this->expires_at);
    }

    public function requiresPassword()
    {
        return !empty($this->password);
    }

    public function checkPassword($password)
    {
        return $this->password === $password;
    }
}
