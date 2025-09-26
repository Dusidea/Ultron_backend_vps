<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class FileController extends AbstractController
{
    #[Route('/files', name: 'files_list')]
    public function list(): JsonResponse
    {
      
        $directory = $this->getParameter('kernel.project_dir') . '/data';

        $result = [];
        foreach (scandir($directory) as $category) {
            if ($category === '.' || $category === '..') {
                continue;
            }

            $path = $directory . '/' . $category;
            if (is_dir($path)) {
                $files = array_diff(scandir($path), ['.', '..']);
                if (!empty($files)) {
                    $result[$category] = array_values($files); 
                }
            }
        }

        return new JsonResponse($result);
  }
	   #[Route('/files/download/{category}/{filename}', name: 'files_download', requirements: ['filename' => '.+'])]
        public function download(string $category, string $filename)
        {
                $directory = $this->getParameter('kernel.project_dir') . '/data';
                $filePath = $directory . '/' . $category . '/' . $filename;

                if (!file_exists($filePath)) {
                        return new Response("File not found: $filePath", 404);
                }

                header('Content-Type: text/plain');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                readfile($filePath);
                exit;
        }
}
