<?php

namespace App\Providers;

use App\Services\PromotionService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class ViewServiceProvider extends ServiceProvider
{
    public function boot(PromotionService $service)
    {
        View::composer(['frontend.*', 'layouts.*', 'welcome'], function ($view) use ($service) {
            $courseId = $view->getData()['__courseId'] ?? null;

            $promo = $service->currentBannerPromo($courseId);
            $view->with('promoBanner', $promo);
            $view->with('promoEndsAt', $service->endsAt($promo));
        });
    }
}
