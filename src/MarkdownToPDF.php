<?php

namespace Wappla\LaravelMarkdownToPDF;

use Exception;
use Parsedown;
use Dompdf\Dompdf;
use Illuminate\Http\Response;
use Illuminate\Support\HtmlString;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\View\Factory as ViewFactory;

class MarkdownToPDF
{
    /**
     * The view factory implementation.
     *
     * @var \Illuminate\Contracts\View\Factory
     */
    protected $view;

    /**
     * The current theme being used when generating pdf files.
     *
     * @var string
     */
    protected $theme = 'default';

    /**
     * The dompdf instance.
     *
     * @var
     */
    protected $dompdf;

    /**
     * File system instance.
     *
     * @var Filesystem
     */
    protected $files;

    /**
     * Determine if the pdf is already rendered.
     *
     * @var bool
     */
    protected $rendered = false;

    /**
     * The paper format: 'letter', 'legal', 'A4', etc.
     *
     * @var
     */
    protected $paper;

    /**
     * portrait or landscape mode.
     *
     * @var
     */
    protected $orientation;

    /**
     * Create a new Markdown to PDF instance.
     *
     * @param \Illuminate\Contracts\View\Factory $view
     * @param Dompdf                             $dompdf
     * @param Filesystem                         $files
     * @param array                              $options
     */
    public function __construct(ViewFactory $view, Dompdf $dompdf, Filesystem $files, array $options = [])
    {
        $this->view = $view;
        $this->dompdf = $dompdf;
        $this->files = $files;
        $this->theme = $options['theme'] ?? 'default';
    }

    /**
     * Parse the given Markdown text into HTML.
     *
     * @param string $text
     *
     * @return \Illuminate\Support\HtmlString
     */
    public static function parse($text)
    {
        $parsedown = new Parsedown();

        return new HtmlString($parsedown->text($text));
    }

    /**
     * Load the theme css to be inserted inside the layout file.
     *
     * @return mixed
     */
    public function loadThemeCSS()
    {
        return view('MarkdownToPDF::themes.'.$this->theme);
    }

    /**
     * Set the theme.
     *
     * @param $theme
     *
     * @return MarkdownToPDF
     */
    public function setTheme($theme)
    {
        $this->theme = $theme;

        return $this;
    }

    /**
     * Get the current theme.
     *
     * @return mixed|string
     */
    public function getTheme()
    {
        return $this->theme;
    }

    /**
     * Load a View and convert to HTML.
     *
     * @param string $view
     * @param array  $data
     * @param array  $mergeData
     * @param string $encoding  Not used yet
     *
     * @return static
     */
    public function loadView($view, $data = [], $mergeData = [], $encoding = null)
    {
        $html = $this->view->make($view, $data, $mergeData)->render();

        return $this->loadHTML($html, $encoding);
    }

    /**
     * Set the paper size (default A4).
     *
     * @param string $paper
     * @param string $orientation
     *
     * @return $this
     */
    public function setPaper($paper, $orientation = 'portrait')
    {
        $this->paper = $paper;
        $this->orientation = $orientation;
        $this->dompdf->setPaper($paper, $orientation);

        return $this;
    }

    /**
     * Return a response with the PDF to show in the browser.
     *
     * @param string $filename
     *
     * @throws Exception
     *
     * @return Response
     */
    public function stream($filename = 'document.pdf')
    {
        $output = $this->output();

        return new Response($output, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$filename.'"',
        ]);
    }

    /**
     * Output the PDF as a string.
     *
     * @throws Exception
     *
     * @return string The rendered PDF as string
     */
    public function output()
    {
        if (!$this->rendered) {
            $this->render();
        }

        return $this->dompdf->output();
    }

    /**
     * Save the PDF to a file.
     *
     * @param string $filename
     * @param string $disk
     *
     * @throws Exception
     */
    public function save($filename = 'document.pdf', $disk = null)
    {
        $disk = $disk ?? env('FILESYSTEM_DRIVER', 'local');

        Storage::disk($disk)->put($filename, $this->output());
    }

    /**
     * Make the PDF downloadable by the user.
     *
     * @param string $filename
     *
     * @throws Exception
     *
     * @return \Illuminate\Http\Response
     */
    public function download($filename = 'document.pdf')
    {
        $output = $this->output();

        return new Response($output, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    /**
     * Load a HTML string.
     *
     * @param string $string
     * @param string $encoding Not used yet
     *
     * @return static
     */
    public function loadHTML($string, $encoding = null)
    {
        $string = $this->convertEntities($string);
        $this->dompdf->loadHtml($string, $encoding);
        $this->rendered = false;

        return $this;
    }

    /**
     * Render the PDF.
     *
     * @throws Exception
     */
    protected function render()
    {
        if (!$this->dompdf) {
            throw new Exception('DOMPDF not created yet');
        }
        $this->dompdf->setPaper($this->paper, $this->orientation);
        $this->dompdf->render();
        $this->rendered = true;
    }

    /**
     * Convert symbols to html entities.
     *
     * @param $subject
     *
     * @return mixed
     */
    protected function convertEntities($subject)
    {
        $entities = [
            '€' => '&#0128;',
            '£' => '&pound;',
        ];
        foreach ($entities as $search => $replace) {
            $subject = str_replace($search, $replace, $subject);
        }

        return $subject;
    }
}
