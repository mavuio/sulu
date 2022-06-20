<?php

namespace Mavu\GlobalBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Sulu\Bundle\MediaBundle\Entity\MediaInterface;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\String\Slugger\AsciiSlugger;

/**
 * @ORM\Entity()
 * @ORM\Table(name="mavu_global_dekor")
 * @Serializer\ExclusionPolicy("all")
 */
class Dekor
{
    public const RESOURCE_KEY = 'dekors';
    public const FORM_KEY = 'dekor_details';
    public const LIST_KEY = 'dekors';
    public const SECURITY_CONTEXT = 'sulu.dekors.dekors';


    
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     *
     * @Serializer\Expose()
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Serializer\Expose()
     */
    private string $name;

    /**
     * @ORM\Column(type="string", nullable=true, length=255)
     *
     * @Serializer\Expose()
     */
    private  $slug;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Serializer\Expose()
     */
    private $blockType;

    /**
     * @ORM\Column(type="text", nullable=true)
     *
     * @Serializer\Expose()
     */
    private $classes;

  /**
     * @ORM\Column(type="text", nullable=true)
     *
     * @Serializer\Expose()
     */
    private $notes;


    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addConstraint(new UniqueEntity([
            'fields' => 'slug',
        ]));
        dd("loadvalidator");

    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name ?? '';
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }



    public function getSlug(): string
    {
        return $this->slug ?? '';
    }

    public function setSlug(?string $slug): void
    {
        if ($slug) {
            $slugger = new AsciiSlugger();
            $safeslug = $slugger->slug($slug);
            $this->slug = $safeslug;
        } else {
            $this->slug=null;
        }
    }

    public function getNotes(): string
    {
        return $this->notes ?? '';
    }

    public function setNotes(?string $notes): void
    {
        $this->notes = $notes;
    }


    public function getBlockType(): ?array
    {
        return explode(",",$this->blockType);
    }

  
    public function setBlockType( $blockType): self
    {
        if (is_string($blockType)) {
            $blockType= [ $blockType ];
        } 
        
        $this->blockType = implode(",",$blockType);
        

        return $this;
    }

    public function getClasses(): ?string
    {
        return $this->classes;
    }

    public function setClasses(?string $classes): self
    {
        $this->classes = $classes;

        return $this;
    }

   

   
}
