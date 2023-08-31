<?php
/**
 * @copyright Copyright © 2023 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

/** @var \BeastBytes\PDF\TCPDF\Document $document */

// remove default header/footer
$document->setPrintHeader(false);
$document->setPrintFooter(false);

// set default monospaced font
$document->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$document->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

// set auto page breaks
$document->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$document->setImageScale(PDF_IMAGE_SCALE_RATIO);

// ---------------------------------------------------------

// set font
$document->SetFont('times', 'BI', 20);

// add a page
$document->AddPage();

// set some text to print
$txt = <<<EOD
Test text
EOD;

// print a block of text using Write()
$document->Write(0, $txt, '', 0, 'C', true, 0, false, false, 0);

