<?php
namespace Wappla\LaravelMarkdownToPDF;

class Facade extends \Illuminate\Support\Facades\Facade
{
    
    /**
     * Defining the laravel facade
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return MarkdownToPDF::class;
    }
}
