## Package still in development..

# Markdown to pdf

Simple package to convert markdown files to PDF in laravel.

## Requirements
- Laravel > 5.5

## Installation

Install the package with composer:

````
 composer require 'wappla/laravel-markdown-to-pdf'
````

This packages uses the auto discovery feature of laravel. So there is no need to register the service provider or alias.

## Usage

The most simple use case without any custom configuration:

create a new blade file (example.blade.php) and extend the markdownToPDF layout.

```
@extends('MarkDownToPDF::layout')
```

## Configuration

Use the standard `php artisan vendor:publish` laravel command an select the correct number or be more specific:

To publish all the configuration and views :
````
php artisan vendor:publish --provider="Wappla\LaravelMarkdownToPDF\MarkdownToPDFServiceProvider"
```

Only publish views:
````
php artisan vendor:publish --tag="markdown-to-pdf-views"
```

Only publish config:

````
php artisan vendor:publish --tag="markdown-to-pdf-config"
```


