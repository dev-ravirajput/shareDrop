<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ShareFile extends Model
{
    protected $fillable = [
        'share_id', 'original_name', 'storage_path', 'mime_type', 'size'
    ];

    public function share()
    {
        return $this->belongsTo(Share::class);
    }

    public function getUrlAttribute()
    {
        return Storage::url($this->storage_path);
    }
}