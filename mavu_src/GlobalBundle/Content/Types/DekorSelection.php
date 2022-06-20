<?php

declare(strict_types=1);

namespace Mavu\GlobalBundle\Content\Types;


use Mavu\GlobalBundle\Entity\Dekor;
use Doctrine\ORM\EntityManagerInterface;
use Sulu\Component\Content\Compat\PropertyInterface;
use Sulu\Component\Content\SimpleContentType;

class DekorSelection extends SimpleContentType
{
    protected EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct('dekor_selection', []);
    }

      /**
     * @return Dekor[]
     */
    public function getContentData(PropertyInterface $property): array
    {
        $ids = $property->getValue();

        if (empty($ids)) {
            return [];
        }

        $dekors = $this->entityManager->getRepository(Dekor::class)->findBy(['id' => $ids]);

        $idPositions = array_flip($ids);
        usort($dekors, function (Dekor $a, Dekor $b) use ($idPositions) {
            return $idPositions[$a->getId()] - $idPositions[$b->getId()];
        });

        return $dekors;
    }

    /**
     * @return array<string, int|null>
     */
    public function getViewData(PropertyInterface $property): array
    {
        return [
            'ids' => $property->getValue(),
        ];
    }
}
