<?php

// src/File/FileUploader.php
namespace Domain\File;


use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\String\Slugger\SluggerInterface;

final class FileUploader
{
    public function __construct(
        private string $uploadDirectory,
        private SluggerInterface $slugger,
        private Filesystem $fs = new Filesystem
    ) {}

    public function uploadBase64(string $base64String, string $category, int $objectId) : string
    {
        [$b64Infos, $b64Data] = explode(';base64,', $base64String);
        $mime = explode(':', $b64Infos)[1];
        $extension = explode('/', $mime)[1];

        $fileBinary = base64_decode($b64Data);

        $tmpFileName = 'upl_' . uniqid();

        $tmpDir =  sys_get_temp_dir() . "/$tmpFileName.tmp";
        file_put_contents($tmpDir, $fileBinary);
        
        $newUploadedFile = new File($tmpDir);

        $finalDir = "uploads\\$category\\$objectId\\";
        $file = $newUploadedFile->move($finalDir, $tmpFileName . ".$extension");

        return $file->getFilename();
    }

    public function retrieveFile(string $category, int $objectId, string $fileName): ?File
    {
        $filePath = $this->uploadDirectory . "/$category/$objectId/$fileName";
        
        if(file_exists($filePath)) {
            return new File($filePath);
        } 
        
        return null;
    }

    public function removeFile(string $category, int $objectId): void
    {
        $this->fs->remove($this->uploadDirectory . "/$category/$objectId");
    }
}