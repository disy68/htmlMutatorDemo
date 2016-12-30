<?php
include_once 'SimpleDom/simple_html_dom.php';

class HtmlFileParser
{

    /**
     *
     * @var string
     */
    private static $DOCUMENT_CLASS = 'simple_html_dom';

    /**
     *
     * @var simple_html_dom
     */
    private $document;

    /**
     *
     * @var string
     */
    private $path;

    public function __construct($path)
    {
        $this->path = $path;
    }

    public function getElementByName($elementName)
    {
        $this->createDocumentIfNeeded();
        
        return $this->document->getElementByTagName($elementName);
    }

    public function saveDocument()
    {
        $this->document->save($this->path);
    }

    private function createDocumentIfNeeded()
    {
        if ($this->document == null || ! is_a($this->document, HtmlFileParser::$DOCUMENT_CLASS, FALSE)) {
            $this->document = file_get_html($this->path, false, null, -1, -1, true, true, DEFAULT_TARGET_CHARSET, false, DEFAULT_BR_TEXT);
        }
    }
}