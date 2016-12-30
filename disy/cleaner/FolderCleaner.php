<?php

class FolderCleaner
{

    private $source;

    private $destination;

    public function __construct($source, $destination)
    {
        $this->source = $source;
        $this->destination = $destination;
    }

    public function cleanAndCopy()
    {
        $this->clean();
        $this->copy();
    }

    private function clean()
    {
        if (! file_exists($this->destination)) {
            mkdir($this->destination);
        } else {
            foreach ($iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->destination, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST) as $item) {
                if ($item->isDir()) {
                    rmdir($item->getPathname());
                } else {
                    unlink($item->getPathname());
                }
            }
        }
    }

    private function copy()
    {
        if (!file_exists($this->source)) {
            unlink($this->destination);
            return;
        }
        foreach ($iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->source, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST) as $item) {
            $newItemOnDest = $this->destination . DIRECTORY_SEPARATOR . $iterator->getSubPathName();
            if ($item->isDir()) {
                if (! file_exists($newItemOnDest)) {
                    mkdir($newItemOnDest);
                }
            } else {
                copy($item, $newItemOnDest);
            }
        }
    }
}