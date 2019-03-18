<?php

namespace App\Providers;

use App\Purchase;
use App\Mail\InvoiceShipped;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(UrlGenerator $url)
    {
        Schema::defaultStringLength(191);

        if ( env('REDIRECT_HTTPS') ) {
            $url->formatScheme('https');
        }

        Purchase::updated(function ($purchase) {
            if ($purchase->status_payment == 'capture' && $purchase->isNotConfirm()) {
                $purchase->completed = Purchase::CONFIRMED;
                $purchase->save();

                // send invoice
                // retry sending email 5 times every 100 ms
                retry(5, function() use ($purchase) {
                    $dataInvoice = Purchase::with('user')->where('invoice_number', $purchase->invoice_number)->firstOrFail();
                    Mail::to($dataInvoice->user)->send(new InvoiceShipped($dataInvoice));
                }, 100);
            }
        });
    }

    public function register()
    {
        if ( env('REDIRECT_HTTPS') ) {
            $this->app['request']->server->set('HTTPS', true);
        }
    }
}
