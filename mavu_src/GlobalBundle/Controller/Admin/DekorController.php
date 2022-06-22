<?php

declare(strict_types=1);

namespace Mavu\GlobalBundle\Controller\Admin;

use Exception;
use Tarsana\Functional as F;
use Mavu\GlobalBundle\Entity\Dekor;
use Mavu\GlobalBundle\Admin\DekorAdmin;

use Doctrine\ORM\EntityManagerInterface;
use Mavu\GlobalBundle\Core\TwClassesCore;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Serializer\Serializer;
use FOS\RestBundle\View\ViewHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sulu\Component\Rest\AbstractRestController;
use Sulu\Component\Security\SecuredControllerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Mavu\GlobalBundle\Admin\DoctrineListRepresentationFactory;


use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Mavu\GlobalBundle\Exception\CustomizableException;


use HandcraftedInTheAlps\RestRoutingBundle\Routing\ClassResourceInterface;
use HandcraftedInTheAlps\RestRoutingBundle\Controller\Annotations\RouteResource;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


/**
 * @RouteResource("dekor")
 */
class DekorController extends AbstractRestController implements ClassResourceInterface, SecuredControllerInterface
{
    private DoctrineListRepresentationFactory $doctrineListRepresentationFactory;
    private EntityManagerInterface $entityManager;

    /**
     * @var TwClassesCore
     */
    private $classesCoreService;
    public function __construct(
        DoctrineListRepresentationFactory $doctrineListRepresentationFactory,
        EntityManagerInterface $entityManager,
        ViewHandlerInterface $viewHandler,
        ?TokenStorageInterface $tokenStorage = null,
        TwClassesCore $classesCoreService,
    ) {
        $this->doctrineListRepresentationFactory = $doctrineListRepresentationFactory;
        $this->entityManager = $entityManager;
        $this->classesCoreService = $classesCoreService;

        parent::__construct($viewHandler, $tokenStorage);
    }

    public function cgetAction(Request $request): Response
    {

        $blockType = $request->query->get('blockType');
        $filters = [];
        if ($blockType) {
            $filters["blockType"] = $blockType;
        }

        $listRepresentation = $this->doctrineListRepresentationFactory->createDoctrineListRepresentation(
            Dekor::RESOURCE_KEY,
            $filters
        );

        return $this->handleView($this->view($listRepresentation));
    }

    public function getAction(int $id): Response
    {
        $dekor = $this->entityManager->getRepository(Dekor::class)->find($id);
        if (!$dekor) {
            throw new NotFoundHttpException();
        }
        return $this->handleView($this->view($dekor));
    }

    public function toArray($entity)
    {

        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, []);
        return $serializer->normalize($entity, null);
    }

    public function putAction(Request $request, int $id): Response
    {
        if ($request->query->get('action') == 'duplicate') {
            $oldRec = $this->entityManager->getRepository(Dekor::class)->find($id);
            $oldData = $this->toArray($oldRec);
            $oldData['name'] = $oldData['name'] . " (copy)";
            $oldData['slug'] = null;
            $dekor = new Dekor();
            $this->mapDataToEntity($oldData, $dekor);
            $this->entityManager->persist($dekor);
        } else {
            $dekor = $this->entityManager->getRepository(Dekor::class)->find($id);
            if (!$dekor) {
                throw new NotFoundHttpException();
            }
            $this->mapDataToEntity($request->request->all(), $dekor);
        }

        $this->updateStylesheet();

        $this->entityManager->flush();

        return $this->handleView($this->view($dekor));
    }

    public function postAction(Request $request): Response
    {
        $dekor = new Dekor();

        $this->mapDataToEntity($request->request->all(), $dekor);
        $this->entityManager->persist($dekor);
        $this->entityManager->flush();
        try {
            $this->updateStylesheet();
        } catch (\Exception $e) {
            //remove again, if it caused an error
            $this->entityManager->remove($dekor);
            $this->entityManager->flush();
            throw $e;
        }

        return $this->handleView($this->view($dekor, 201));
    }

    public function deleteAction(int $id): Response
    {
        /** @var Dekor $dekor */
        $dekor = $this->entityManager->getReference(Dekor::class, $id);
        $this->entityManager->remove($dekor);
        $this->entityManager->flush();
        $this->updateStylesheet();

        return $this->handleView($this->view(null, 204));
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function mapDataToEntity(array $data, Dekor $entity): void
    {


        $entity->setName($data['name']);
        $entity->setBlockType($data['blockType']);
        $entity->setClasses($data['classes']);
        $entity->setNotes($data['notes']);
        $entity->setSlug($data['slug']);
    }

    /**
     * @throws CustomizableException
     */
    function updateStylesheet()
    {


        $dekors = $this->entityManager->getRepository(Dekor::class)->findAll();

        $strings = array_map(function ($dekor) {
            return $this->getClassesForDekor($dekor);
        }, $dekors);


        $content = implode("\n", $strings);
        // echo $content;
        try {
            $this->classesCoreService->writeDekorStylesheet($content);
        } catch (\Exception $e) {
            throw new CustomizableException($e->getMessage());
        }
    }

    public function getClassesForDekor($dekor)
    {
        $classString = $dekor->getClasses() ?? "";

        $classString = $this->classesCoreService->removeComments($classString);
        $classString = $this->classesCoreService->customizeBreakpointPrefixes($classString, $this->classesCoreService->getDefaultBreakpoints());

        $classes = $this->getClassesFromString($classString);


        $css = F\Stream::of($classes)
            ->map(function ($class) {
                return $this->splitClassIntoPrefix($class);
            })
            ->groupBy(function ($class_w_prefix) {
                [$prefix, $_] = $class_w_prefix;
                return $prefix;
            })
            ->toPairs()
            ->map(function ($class_w_prefix) {
                [$prefix, $vals] = $class_w_prefix;

                $twClassStr = F\Stream::of($vals)
                    ->map(function ($val) {
                        return $val[1];
                    })
                    ->join(" ")
                    ->result();

                $twClassStr = trim($twClassStr);
                if ($twClassStr) {
                    if ($prefix) {
                        return ".$prefix { @apply $twClassStr; }";
                    } else {
                        return "@apply $twClassStr;";
                    }
                }
                return "";
            })
            ->join("\n")
            ->result();


        if (trim($css)) {
            $selector = ".preset_{$dekor->getId()}";
            $slug = $dekor->getSlug();
            if ($slug) {
                $selector .= ", .preset_{$slug}";
            }
            return "
            /*{$dekor->getName()}: */ 
            {$selector} {
                {$css}
            }
            ";
        } else {
            return "";
        }
    }


    public function splitClassIntoPrefix($str)
    {
        if (preg_match("/^(c.)\:(.*)$/", $str, $m)) {
            return [$m[1], $m[2]];
        } else {
            return ["", $str];
        }
    }


    public function getClassesFromString(string $str)
    {
        return preg_split("/[\s]+/", $str);
    }


    public function getSecurityContext(): string
    {
        return DekorAdmin::SECURITY_CONTEXT;
    }
}
