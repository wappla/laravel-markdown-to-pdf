## Package still in development..

# Markdown to pdf

Simple package to convert markdown files to PDF in laravel.

## Requirements
- Laravel > 5.5

## Installation

Install the package with composer:

```
 composer require 'wappla/laravel-markdown-to-pdf'
```

This packages uses the auto discovery feature of laravel. So there is no need to register the service provider or alias.

## Usage

Create a blade view and add the markdown to pdf component. Between the component element you can start writing markdown.

Below i created a simple example.blade.php :
```
@component('MarkdownToPDF::layout')
# Hello
@endcomponent
```

### To view this as a PDF file in your browser you dan do:

```php
    return \MarkdownToPDF::loadView('example')->stream();
```

The standard directory for loading views is resources/views. No need to add the blade.php extension. If you would like to specify a custom directory you can use the dot notation.

```php
    return \MarkdownToPDF::loadView('custom-directory.example')->stream();
```

### Store this view in storage

Specifiy a custom filename or leave blank to save your pdf in storage.

```php
    return \MarkdownToPDF::loadView('example')->save('awesome-file.pdf');
```

The pdf will be stored based on your Laravel filesystem configuration. If you would like to specify a custom location you can create your own disk and add a second parameter to the save method.

The below example stores the pdf file on the public disk:

```php
    return \MarkdownToPDF::loadView('example')->save('awesome-file.pdf', 'public');
```


## Configuration

Use the standard `php artisan vendor:publish` laravel command an select the correct number or be more specific:

To publish all the configuration and views:

```
php artisan vendor:publish --provider="Wappla\LaravelMarkdownToPDF\MarkdownToPDFServiceProvider"
```

Only publish views:

```
php artisan vendor:publish --tag="markdown-to-pdf-views"
```

Only publish config:

```
php artisan vendor:publish --tag="markdown-to-pdf-config"
```


