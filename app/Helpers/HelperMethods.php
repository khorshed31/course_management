<?php

use App\Models\Setting;
use App\Models\Promotion;
use Carbon\Carbon;


function redirectIfError($error, $with_input = null)
{
    if (request()->dev == 1) {
        throw $error;
    }
    if ($with_input) {
        return redirect()->back()->withInput(request()->except('image'))->withError($error->getMessage());
    }
    return redirect()->back()->withError($error->getMessage());
}


if (! function_exists('setting')) {
    function setting(string $key, $default = null) {
        static $cache = [];
        if (array_key_exists($key, $cache)) return $cache[$key];

        $val = optional(Setting::query()->where('key',$key)->first())->value;
        return $cache[$key] = $val ?? $default;
    }
}



if (!function_exists('getPromotion')) {
    function getPromotion()
    {
        $promotion = Promotion::where('course_id','0')
                                                ->where('status','1')->first();
        if (!empty($promotion)) :
            return $promotion;
        endif;
    }
}

if (! function_exists('promotionLabel')) {
    /**
     * "SAVE 45.49 $" or "SAVE 20 %"
     */
    function promotionLabel(Promotion $promo): string
    {
        return $promo->discount_value_type === 'percentage'
            ? 'SAVE ' . rtrim(rtrim(number_format($promo->discount_value, 2, '.', ''), '0'), '.') . ' %'
            : 'SAVE ' . number_format($promo->discount_value, 2) . ' $';
    }
}

if (! function_exists('promotionExpiryIso')) {
    /**
     * Returns ISO8601 string for countdown JS.
     */
    function promotionExpiryIso(Promotion $promo): ?string
    {
        if ($promo->discount_type === 'timer' && $promo->end_time) {
            return $promo->end_time->toIso8601String();
        }
        if ($promo->discount_type === 'special_day' && $promo->end_date) {
            return $promo->end_date->copy()->endOfDay()->toIso8601String();
        }
        return null;
    }
}





function fdate($value, $format = null)
{
    if ($value == '') {
        return '';
    }

    if ($format == null) {
        $format = 'd/m/Y';
    }

    return \Carbon\Carbon::parse($value)->format($format);
}

use Illuminate\Support\Facades\Auth;

if (!function_exists('isAdmin')) {
    /**
     * Check if the current user is an admin.
     *
     * @param  \App\Models\User|null  $user
     * @return bool
     */
    function isAdmin($user = null): bool
    {
        $user = $user ?: Auth::user();

        if (!$user) {
            return false;
        }

        return $user->role === 'admin';
    }
}

if (!function_exists('isStudent')) {
    /**
     * Check if the current user is a student.
     *
     * @param  \App\Models\User|null  $user
     * @return bool
     */
    function isStudent($user = null): bool
    {
        $user = $user ?: Auth::user();

        if (!$user) {
            return false;
        }

        return $user->role === 'student';
    }
}

function getInWord($number)
{
    $decimal = round($number - ($no = floor($number)), 2) * 100;
    $hundred = null;
    $digits_length = strlen($no);
    $i = 0;
    $str = array();
    $words = array(0 => '', 1 => 'One', 2 => 'Two',
        3 => 'Three', 4 => 'Four', 5 => 'Five', 6 => 'Six',
        7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
        10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve',
        13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen',
        16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen',
        19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty',
        40 => 'Forty', 50 => 'Fifty', 60 => 'Sixty',
        70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety');
    $digits = array('', 'Hundred','Thousand','Lakh', 'Crore');
    while( $i < $digits_length ) {
        $divider = ($i == 2) ? 10 : 100;
        $number = floor($no % $divider);
        $no = floor($no / $divider);
        $i += $divider == 10 ? 1 : 2;
        if ($number) {
            $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
            $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
            $str [] = ($number < 21) ? $words[$number].' '. $digits[$counter]. $plural.' '.$hundred:$words[floor($number / 10) * 10].' '.$words[$number % 10]. ' '.$digits[$counter].$plural.' '.$hundred;
        } else $str[] = null;
    }
    $Taka = implode('', array_reverse($str));
    $poysa = ($decimal) ? " and " . ($words[$decimal / 10] . " " . $words[$decimal % 10]) . ' poysa' : '';

    return ($Taka ? $Taka . 'taka ' : '') . $poysa ;
}


