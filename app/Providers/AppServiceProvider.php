<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        view()->share(['general' => \App\GeneralSetting::first()]);
        view()->share(['lang' => \App\Language::all()]);
        view()->share(['social' => \App\Frontend::where('key', 'social.item')->get()]);

        view()->composer('partials.seo', function ($view) {
            $seo = \App\Frontend::where('key', 'seo')->first();
            $view->with([
                'seo' => $seo ? $seo->value : $seo,
            ]);
        });
        view()->composer(activeTemplate().'partials.front_br',  function ($view) {
            $bcrumb = \App\Frontend::where('key', 'breadcrumb')->first();
            $view->with([
                'bcrumb' => $bcrumb->value ,
            ]);
        });

        view()->composer(activeTemplate().'layouts.master',  function ($view) {
            $footer = \App\Frontend::where('key', 'footer.title')->first();
            $contact= \App\Frontend::where('key', 'contact.post')->first();
            $view->with([
                'footer' => $footer->value ,
                'contact' => $contact->value ,
            ]);

        });

        view()->composer('admin.partials.sidenav', function ($view) {
            $view->with([
                'banned_users_count'           => \App\User::banned()->count(),
                'email_unverified_users_count' => \App\User::emailUnverified()->count(),
                'sms_unverified_users_count'   => \App\User::smsUnverified()->count(),
                'pending_withdrawals_count'    => \App\Withdrawal::pending()->count(),
                'pending_ticket_count'         => \App\SupportTicket::whereIN('status', [0,2])->count()
            ]);
        });
    }
}
