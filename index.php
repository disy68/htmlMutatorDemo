<?php
// Demo setup
include_once 'disy/cleaner/FolderCleaner.php';
$source = "html";
$dest = "mutated_html";

$cleaner = new FolderCleaner($source, $dest);
$cleaner->cleanAndCopy();


// Usage

// sets cli usage used for output formatting
$cliUsage = php_sapi_name() == 'cli' ? true : false;

// Could come from parameters:
// argv -> cli
// post/get -> http

$input_path = "mutated_html";
$imagePath = "mutated_html/images";
$extensions = array(
    "html"
);
// includes
include_once 'disy/mutator/HtmlElementMutator.php';
include_once 'disy/mutator/iterator/filter/FileExtensionFilterIterator.php';
include_once 'disy/mutator/parser/HtmlParser.php';

// recursive directory iterator with extension filter
$recursiveIteratorIterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($input_path));
$filterIterator = new FileExtensionFilterIterator($recursiveIteratorIterator, $extensions);

// mutator with the iterable html files and path for image folder
$mutator = new HtmlElementMutator($filterIterator, $imagePath, $cliUsage);
// run
$mutator->mutateAll();
