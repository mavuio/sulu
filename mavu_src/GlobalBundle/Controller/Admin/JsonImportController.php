<?php

namespace Mavu\GlobalBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;


class JsonImportController extends AbstractController
{
    #[Route('/admin/import/page', name: 'page_import')]
    public function page_import(Request $request): JsonResponse
    {
        return $this->json([$request]);
    }
}
