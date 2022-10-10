<?php

namespace Mavu\GlobalBundle\Controller\Admin;

use Mavu\GlobalBundle\Core\JsonImportCore;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class JsonImportController extends AbstractController
{
    #[Route('/admin/import/page', name: 'page_import')]
    public function page_import(Request $request, JsonImportCore $jsonImportCore): JsonResponse
    {

        $data = json_decode($request->getContent(), 1);

        $page = $jsonImportCore->importPage($data);

        $res = ["status" => 'ok'];
        return $this->json($res);
    }
}
