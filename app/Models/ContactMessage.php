<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactMessage extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name','email','phone','social','message','ip','user_agent',
        'status','is_starred','first_replied_at','reply_count',
    ];

    protected $casts = [
        'is_starred' => 'boolean',
        'first_replied_at' => 'datetime',
    ];

    public function replies() {
        return $this->hasMany(ContactReply::class);
    }

    public function scopeFilter($q, array $f) {
        if (!empty($f['status'])) $q->where('status', $f['status']);
        if (!empty($f['q'])) {
            $q->where(function($w) use ($f) {
                $w->where('name','like',"%{$f['q']}%")
                  ->orWhere('email','like',"%{$f['q']}%")
                  ->orWhere('message','like',"%{$f['q']}%");
            });
        }
        if (!empty($f['star'])) $q->where('is_starred', true);
        return $q;
    }
}
