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

        $content = $jsonImportCore->preParseJsonSource($request->getContent());
        $data = json_decode($content, 1);

        $page = $jsonImportCore->importPage($data);

        $res = ["status" => 'ok', 'page' => $page->getUuId(), "data" => $data];
        return $this->json($res);
    }

    #[Route('/admin/export/page', name: 'page_export')]
    public function page_export(Request $request, JsonImportCore $jsonImportCore): JsonResponse
    {

        $page = $jsonImportCore->exportPage($request->query->get('url'), ($request->query->get('with_extensions') == "1"));
        if (array_key_exists("url", $page)) {
            $res = ["status" => 'ok', "page" => $page];
        } else {
            $res = ['status' => 'error', "page" => null];
        }
        return $this->json($res);
    }
}
