<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Book extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uploaded_by', 'title', 'slug', 'author', 'description',
        'cover_path', 'file_path', 'pages', 'downloads_count',
        'price', 'status', 'published_at'
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'price' => 'decimal:2',
        'downloads_count' => 'integer',
        'pages' => 'integer',
    ];

    protected static function booted()
    {
        static::creating(function (Book $book) {
            if (blank($book->slug)) {
                $book->slug = Str::slug($book->title) . '-' . Str::random(6);
            }
        });
    }

    // Relationships
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function purchases(){ return $this->hasMany(\App\Models\BookPurchase::class); }
    public function isPurchasedBy($user): bool {
    if (!$user) return false;
    return $this->purchases()->where('user_id', $user->id)->where('status','paid')->exists();
    }

    // Scopes
    public function scopePublished($q)
    {
        $q->where('status', 'published')
          ->whereNotNull('published_at')
          ->where('published_at', '<=', now());
    }

    // Helpers
    public function coverUrl(): ?string { return $this->cover_path ? asset($this->cover_path) : null; }
    public function pdfUrl(): string { return asset($this->file_path); }
}


