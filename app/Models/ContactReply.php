<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactReply extends Model
{
    protected $fillable = [
        'contact_message_id','admin_id','to_email','subject','body',
    ];

    public function message() {
        return $this->belongsTo(ContactMessage::class, 'contact_message_id');
    }

    public function admin() {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
