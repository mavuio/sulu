<?php

namespace Mavu\HhcBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Mavu\HhcBundle\Repository\DekorRepository;

#[ORM\Entity(repositoryClass: DekorRepository::class)]
class Dekor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }
}
