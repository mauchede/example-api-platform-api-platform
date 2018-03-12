<?php

declare(strict_types=1);

namespace AppBundle\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     iri="http://schema.org/ImageGallery",
 * )
 *
 * @ORM\Entity
 */
class Gallery extends Thing
{
    /**
     * @ORM\ManyToMany(
     *     orphanRemoval=true,
     *     targetEntity="Image",
     * )
     *
     * @var Collection
     */
    private $images;

    /**
     * @ApiProperty(
     *     iri="http://schema.org/primaryImageOfPage"
     * )
     *
     * @ORM\OneToOne(
     *     fetch="EAGER",
     *     orphanRemoval=true,
     *     targetEntity="Image"
     * )
     *
     * @var Image|null
     */
    private $mainImage;

    /**
     * @ApiProperty(
     *     iri="http://schema.org/name",
     * )
     *
     * @Assert\NotBlank
     * @Assert\Type(
     *     type="string",
     * )
     *
     * @ORM\Column(
     *     type="text",
     * )
     *
     * @var null|string
     */
    private $name;

    public function __construct()
    {
        $this->images = new ArrayCollection();
    }

    /**
     * @param Image $image
     */
    public function addImage(Image $image): void
    {
        $this->images->add($image);
    }

    /**
     * @return Image[]
     */
    public function getImages(): array
    {
        return \array_values($this->images->toArray());
    }

    /**
     * @return Image|null
     */
    public function getMainImage(): ?Image
    {
        return $this->mainImage;
    }

    /**
     * @return null|string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param Image $image
     */
    public function removeImage(Image $image): void
    {
        $this->images->removeElement($image);
    }

    /**
     * @param Image|null $mainImage
     */
    public function setMainImage(Image $mainImage = null): void
    {
        $this->mainImage = $mainImage;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
