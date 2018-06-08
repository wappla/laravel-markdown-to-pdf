<!doctype html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
</head>
<style>
    {{ \Wappla\LaravelMarkdownToPDF\Facade::loadThemeCSS() }}
</style>
<body>
{{ \Wappla\LaravelMarkdownToPDF\MarkdownToPDF::parse($slot) }}
</body>
</html>