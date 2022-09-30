<?php

namespace Mavu\GlobalBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Mavu\GlobalBundle\Core\DekorCore;
use JMS\Serializer\Annotation as Serializer;

use Sulu\Bundle\MediaBundle\Entity\MediaInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * @Serializer\ExclusionPolicy("all")
 */
#[ORM\Entity]
#[ORM\Table(name: 'mavu_global_dekor')]
class Dekor
{
    public const RESOURCE_KEY = 'dekors';
    public const FORM_KEY = 'dekor_details';
    public const LIST_KEY = 'dekors';
    public const SECURITY_CONTEXT = 'sulu.dekors.dekors';
    /**
     *
     * @Serializer\Expose()
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[UniqueEntity(fields: "slug", message: "This Slug is already taken.")]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;
    /**
     * @Serializer\Expose()
     */
    #[ORM\Column(type: 'string', length: 255)]
    private string $name;
    /**
     * @Serializer\Expose()
     */
    #[ORM\Column(type: 'string', nullable: true, length: 255, unique: true)]
    private $slug;
    /**
     * @Serializer\Expose()
     */
    #[ORM\Column(type: 'string', length: 255)]
    private $blockType;
    /**
     * @Serializer\Expose()
     */
    #[ORM\Column(type: 'text', nullable: true)]
    private $classes;
    /**
     * @Serializer\Expose()
     */
    #[ORM\Column(type: 'text', nullable: true)]
    private $notes;
    /**
     *
     * @Serializer\Expose()
     */
    #[ORM\Column]
    private ?bool $ignoreDefaults = null;

    /**
     *
     * @Serializer\Expose()
     */
    #[ORM\Column(nullable: true)]
    private ?bool $useRawCss = null;
    /**
     *
     * @Serializer\Expose()
     */

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $rawCss = null;

    // public static function loadValidatorMetadata(ClassMetadata $metadata)
    // {
    //     $metadata->addConstraint(new UniqueEntity([
    //         'fields' => 'slug',
    //     ]));
    // }
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
            $this->slug = null;
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
        return explode(",", $this->blockType);
    }
    public function setBlockType($blockType): self
    {
        if (is_string($blockType)) {
            $blockType = [$blockType];
        }

        $this->blockType = implode(",", $blockType);


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


    public function isDefaultStyle(): ?bool
    {
        return $this->slug == DekorCore::getSlugForContentTypeName($this->blockType);
    }




    public function isIgnoreDefaults(): ?bool
    {
        return $this->ignoreDefaults;
    }

    public function setIgnoreDefaults(bool $ignoreDefaults): self
    {
        $this->ignoreDefaults = $ignoreDefaults;

        return $this;
    }


    public function getIgnoreDefaults(): bool
    {
        return $this->ignoreDefaults ? true : false;
    }

    public function getUseRawCss(): ?bool
    {
        return $this->useRawCss;
    }

    public function setUseRawCss(?bool $useRawCss): self
    {
        $this->useRawCss = $useRawCss;

        return $this;
    }

    public function getRawCss(): ?string
    {
        return $this->rawCss;
    }

    public function setRawCss(?string $rawCss): self
    {
        $this->rawCss = $rawCss;

        return $this;
    }
}
