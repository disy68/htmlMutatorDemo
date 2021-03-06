<?php

class HtmlElementMutator
{
    private $lineBreak;
    private $horizontalBreak;
    private $iterator;
    private $imageFolderPath;

    public function __construct(Iterator $iterator, $imageFolderPath, $cliUsage = false)
    {
        $this->iterator = $iterator;
        $this->imageFolderPath = $imageFolderPath;
        $this->setBreaks($cliUsage);
    }

    public function mutateAll()
    {
        $start = microtime(true);
        $count = 0;
        foreach ($this->iterator as $fileInfo) {
            $this->mutate($fileInfo);
            $count++;
        }
        echo $this->horizontalBreak 
            . 'Mutation done. Mutated: ' . $count . ' items.' . $this->lineBreak
            . 'Elapsed: ' . (microtime(true) - $start) . 'ms. ' 
            . $this->horizontalBreak;
    }
    
    private function setBreaks($cliUsage)
    {
        if ($cliUsage) {
            $this->lineBreak = "\n";
            $this->horizontalBreak = "\n-----------\n";
        } else {
            $this->lineBreak = "<br />";
            $this->horizontalBreak = "<hr />";
        }
    }

    private function mutate(SplFileInfo $fileInfo)
    {
        $fileName = $this->getFileNameWithoutExtension($fileInfo);
        echo $this->horizontalBreak . $fileName . $this->horizontalBreak;
        $parser = new HtmlFileParser($fileInfo->getRealPath());
        
        $this->mutateAnchor($parser->getElementByName("a"), $fileName);
        $this->mutateImage($parser->getElementByName("img"), $fileName);
        
        $parser->saveDocument();
    }

    private function getFileNameWithoutExtension($fileInfo)
    {
        $extensionWithDot = '.' . $fileInfo->getExtension();
        return str_replace($extensionWithDot, '', $fileInfo->getFilename());
    }

    private function mutateAnchor(simple_html_dom_node $anchorNode, $fileName)
    {
        $anchorHrefAttribute = $anchorNode->href;
        if (! is_null($anchorHrefAttribute)) {
            $newHref = $this->mutateFileName($anchorHrefAttribute, $fileName);
            $anchorNode->href = $newHref;
            
            echo 'old href: ' . $anchorHrefAttribute . $this->lineBreak;
            echo 'new href: ' . $newHref . $this->lineBreak;
        } else {
            echo 'href: n/a <br/>';
        }
    }

    private function mutateFileName($oldNameWithExtension, $newNameWithoutExtension)
    {
        $nameParts = explode('.', $oldNameWithExtension);
        $size = count($nameParts);
        if ($size >= 2) {
            $extension = $nameParts[$size - 1];
            return $newNameWithoutExtension . '.' . $extension;
        }
        return $oldNameWithExtension;
    }
    
    private function mutateImage(simple_html_dom_node $imageNode, $fileName)
    {
        $imageSrcAttribute = $imageNode->src;
        if (! is_null($imageSrcAttribute)) {
            $newSrc = $this->mutateFileName($imageSrcAttribute, $fileName);
            $imageNode->src = $newSrc;
            
            echo 'old src: ' . $imageSrcAttribute . $this->lineBreak;
            echo 'new src: ' . $newSrc . $this->lineBreak;
        } else {
            echo 'src: n/a' . $this->lineBreak;
        }
        $this->renameCorrespondingImage($imageSrcAttribute, $newSrc);
    }
    
    private function renameCorrespondingImage($oldImageName, $newImageName) {
        $oldImagePath = $this->imageFolderPath . DIRECTORY_SEPARATOR . $oldImageName;
        if (file_exists($oldImagePath)) {
            $newImagePath = $this->imageFolderPath . DIRECTORY_SEPARATOR . $newImageName;
            echo 'File exists: ' . $oldImagePath . $this->lineBreak;
            rename($oldImagePath, $newImagePath);
            echo 'File renamed: ' . $newImagePath . $this->lineBreak;
        } else {
            echo 'File doesn\'t exists: ' . $oldImagePath . $this->lineBreak;
        }
    }
}