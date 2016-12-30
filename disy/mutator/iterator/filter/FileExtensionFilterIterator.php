<?php

class FileExtensionFilterIterator extends FilterIterator
{

    private $extensions;

    private $iterator;

    public function __construct(Iterator $iterator, $extensions)
    {
        $this->iterator = $iterator;
        $this->extensions = $extensions;
        parent::__construct($iterator);
        $this->rewind();
    }

    public function accept()
    {
        $current = $this->getInnerIterator()->current();
        $extension = $current->getExtension();
        return in_array($extension, $this->extensions);
    }
}