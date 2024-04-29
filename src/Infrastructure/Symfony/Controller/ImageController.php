<?php

namespace Infrastructure\Symfony\Controller;

use Domain\File\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ImageController extends AbstractController
{
    #[Route('/api/images/{category}/{objectId}/{fileName}', name: 'app.image.get', methods: ['GET'])]
    public function index(
        string $category,
        int $objectId,
        string $fileName,
        FileUploader $fu
    ): BinaryFileResponse | Response
    {
        $image = $fu->retrieveFile($category, $objectId, $fileName);

        if(!is_null($image)) {
            return new BinaryFileResponse($image);
        }

        return new Response(null, 404);
    }
}
