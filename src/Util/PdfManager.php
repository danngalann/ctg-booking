<?php


namespace App\Util;


use Dompdf\Dompdf;
use Dompdf\Options;

class PdfManager
{
    private string $fileName;
    private Options $options;
    private Dompdf $dompdf;

    public function initialize(string $fileName): void
    {
        $this->fileName = $fileName;
        $this->options = new Options();
        $this->options->set('defaultFont', 'Roboto');
        $this->options->set('isRemoteEnabled', true);

        $this->dompdf = new Dompdf($this->options);
    }

    public function loadHtml(string $html): void
    {
        $this->dompdf->loadHtml($html);
    }

    public function setOptions($attributes, $value = null): void
    {
        $this->options->set($attributes, $value);
        $this->dompdf->setOptions($this->options);
    }

    public function setPaper($size = "A4", $orientation = "portrait"): void
    {
        $this->dompdf->setPaper($size, $orientation);
    }

    public function download(): void
    {
        $this->dompdf->render();
        $this->dompdf->stream($this->fileName, [
            "Attachment" => true
        ]);
    }
}