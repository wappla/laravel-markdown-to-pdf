<?php

namespace Wappla\LaravelMarkdownToPDF;

use Dompdf\Dompdf;
use Illuminate\Support\ServiceProvider;

class MarkdownToPDFServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/markdown-to-pdf.php' => config_path('markdown-to-pdf.php'),
        ], 'markdown-to-pdf-config');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'MarkdownToPDF');

        $this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/markdownToPDF'),
        ], 'markdown-to-pdf-views');
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->app->singleton(MarkdownToPDF::class, function ($app) {
            $config = $app->make('config');
            $dompdfOptions = $config->get('markdown-to-pdf.dompdf');

            $options = [];
            foreach ($dompdfOptions as $key => $value) {
                $key = strtolower(str_replace('DOMPDF_', '', $key));
                $options[$key] = $value;
            }

            return new MarkdownToPDF($app->make('view'), new Dompdf($options), $app->make('files'), [
                'theme' => $config->get('markdown-to-pdf.theme', 'default'),
                'paths' => $config->get('markdown-to-pdf.paths', []),
            ]);
        });
    }
}
