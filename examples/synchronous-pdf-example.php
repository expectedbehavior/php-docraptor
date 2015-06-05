<?php
// From the root, run:
//   composer install
//   php -f examples/sychronous-pdf-example.php

require_once(__DIR__ . '/../vendor/autoload.php');

$docRaptor = new DocRaptor\ApiWrapper("YOUR_API_KEY_HERE");
$docRaptor->setDocumentContent('<h1>Hello!</h1>')->setDocumentType('pdf')->setTest(true)->setName('synchronous-pdf-example.pdf');
$file = $docRaptor->fetchDocument();
$file_location = __DIR__ . '/test.pdf';
file_put_contents($file_location, $file);
echo(sprintf("File written to: %s\n", $file_location));
