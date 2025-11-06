<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Module\Dokani\Models\Package;
use Module\Permission\Models\Permission;
use Module\Dokani\Models\BankInformation;
use Module\Dokani\Models\BusinessSetting;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;


class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function scopeDokani($query)
    {
        if (auth()->id() == 1) {
            return;
        }
        return $query->where('id', auth()->user()->type == 'owner' ? auth()->id() : auth()->user()->dokan_id);
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function bookPurchases()
    {
        return $this->hasMany(BookPurchase::class);
    }

    public function purchasedBooks()
    {
        // if you prefer a direct list of books
        return $this->belongsToMany(Book::class, 'book_purchases')
                    ->withTimestamps()
                    ->withPivot(['status','amount_cents']);
    }



    // public function businessInfo()
    // {
    //     BusinessSetting::where('id', auth()->user()->type == 'owner' ? auth()->id() : auth()->user()->dokan_id)->first();
    //     return $this->belongsTo(BusinessSetting::class, 'dokan_id','user_id');
    // }

    public function enrollments() {
        return $this->hasMany(CourseEnrollment::class, 'user_id');
    }


    public function scopeSearchByField($query, $filed_name)
    {
        $query->when(request()->filled($filed_name), function ($qr) use ($filed_name) {
            $qr->where($filed_name, request()->$filed_name);
        });
    }

    public function scopeSearchByFields($query, $filed_names)
    {
        foreach ($filed_names as $key => $filed_name) {

            $query->when(request()->filled($filed_name), function($qr) use($filed_name) {
                $qr->where($filed_name, request()->$filed_name);
            });
        }

    }


    
    public function sendPasswordResetNotification($token)
    {
        $url = url(route('password.reset', ['token' => $token, 'email' => $this->email], false));

        $this->notify(new class($url) extends ResetPassword {
            public function __construct(public string $url) {}
            public function toMail($notifiable)
            {
                return (new MailMessage)
                    ->subject('Reset your password')
                    ->line('Click the button below to reset your password.')
                    ->action('Reset Password', $this->url)
                    ->line('If you did not request a password reset, no further action is required.');
            }
        });
    }


}
