<?php

declare(strict_types=1);

namespace AppBundle\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource(
 *     collectionOperations={},
 *     iri="http://schema.org/Thing",
 *     itemOperations={},
 * )
 *
 * @ORM\DiscriminatorColumn(
 *     name="class",
 *     type="string",
 * )
 * @ORM\Entity
 * @ORM\InheritanceType(
 *     "JOINED",
 * )
 */
abstract class Thing
{
    /**
     * @ApiProperty(
     *     writable=false,
     * )
     *
     * @ORM\Column(
     *     type="guid",
     * )
     * @ORM\GeneratedValue(
     *     strategy="UUID",
     * )
     * @ORM\Id
     *
     * @var string
     */
    protected $id;

    /**
     * @return string
     */
    public function getId(): ?string
    {
        return $this->id;
    }
}
