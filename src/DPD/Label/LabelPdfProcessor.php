<?php

namespace FBF\DPD\Label;

use FBF\DPD\Entity\ParcelLabel;
use Imagick;
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException;
use setasign\Fpdi\PdfParser\Filter\FilterException;
use setasign\Fpdi\PdfParser\PdfParserException;
use setasign\Fpdi\PdfParser\Type\PdfTypeException;
use setasign\Fpdi\PdfReader\PdfReaderException;
use Smalot\PdfParser\Parser;

class LabelPdfProcessor
{
    public function __construct(
        private readonly string $tempDir = '/tmp/labels'
    ) {
        if (! is_dir($this->tempDir)) {
            mkdir($this->tempDir, 0777, true);
        }
    }

    /**
     * @throws FilterException
     * @throws CrossReferenceException
     * @throws PdfParserException
     * @throws PdfTypeException
     * @throws PdfReaderException
     */
    public function splitAndExtractParcelNumbers(string $pdfContent): array
    {
        $tempFile = tempnam($this->tempDir, 'label_').'.pdf';
        file_put_contents($tempFile, $pdfContent);

        $fpdi = new Fpdi;
        $pageCount = $fpdi->setSourceFile($tempFile);

        $parcelLabels = [];

        for ($i = 1; $i <= $pageCount; $i++) {
            $fpdi = new Fpdi;
            $fpdi->setSourceFile($tempFile);
            // Importuj stronę i pobierz jej rozmiary
            $size = $fpdi->getTemplateSize($fpdi->importPage($i));

            $fpdi->addPage('P', [$size['width'], $size['height']]);
            $tplId = $fpdi->importPage($i);
            $fpdi->useTemplate($tplId);

            $pdfPagePath = tempnam($this->tempDir, "label_page_{$i}").'.pdf';
            $fpdi->output($pdfPagePath, 'F');

            $parcelNumber = $this->extractParcelNumberFromPdf($pdfPagePath);

            if ($parcelNumber) {
                $jpgPagePath = $this->convertPdfPageToJpg($pdfPagePath, 0, tempnam($this->tempDir, "label_page_{$i}_".$parcelNumber).'.jpg');

                $parcelLabels[] = new ParcelLabel(
                    parcelno: $parcelNumber,
                    pdfPath: realpath($pdfPagePath),
                    jpgPath: $jpgPagePath ? realpath($jpgPagePath) : null
                );
            }
        }

        unlink($tempFile);

        return $parcelLabels;
    }

    private function extractParcelNumberFromPdf(string $pdfFile): ?string
    {
        $parser = new Parser;
        $pdf = $parser->parseFile($pdfFile);
        $text = $pdf->getText();

        $text = str_replace(' ', '', $text);

        if (preg_match_all('/Consignment(\d{14})/', $text, $matches)) {
            return end($matches[1]);
        }

        return null;
    }

    private function convertPdfPageToJpg(string $pdfFilePath, int $pageNumber, string $outputPath): ?string
    {
        if (! class_exists('Imagick')) {
            return null;
        }

        $imagick = new Imagick;
        $imagick->setResolution(300, 300);
        $imagick->readImage($pdfFilePath);
        $imagick->setImageFormat('jpg');
        $imagick->setImageCompressionQuality(100);
        $imagick->setImageColorspace(Imagick::COLORSPACE_RGB);

        $imagick->writeImage($outputPath);

        $imagick->clear();
        $imagick->destroy();

        return $outputPath;
    }
}
