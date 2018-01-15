<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use Doctrine\ORM\Mapping as ORM;
use League\Flysystem\File;
use Symfony\Component\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity
 */
abstract class Media extends Thing
{
    /**
     * @ApiProperty(
     *     iri="http://schema.org/contentUrl",
     * )
     *
     * @var null|string
     */
    protected $contentUrl;

    /**
     * @var null|File
     */
    protected $file;

    /**
     * @ApiProperty(
     *     iri="http://schema.org/fileFormat"
     * )
     *
     * @ORM\Column(
     *     type="text"
     * )
     *
     * @var null|string
     */
    protected $format;

    /**
     * @ApiProperty(
     *     iri="http://schema.org/contentSize",
     * )
     *
     * @ORM\Column(
     *     type="integer",
     * )
     *
     * @Serializer\Groups({
     *     "media_output",
     * })
     *
     * @var int|null
     */
    protected $size;

    /**
     * @return null|string
     */
    public function getContentUrl(): ?string
    {
        return $this->contentUrl;
    }

    /**
     * @return null|File
     */
    public function getFile(): ?File
    {
        return $this->file;
    }

    /**
     * @return null|string
     */
    public function getFormat(): ?string
    {
        return $this->format;
    }

    /**
     * @return int|null
     */
    public function getSize(): ?int
    {
        return $this->size;
    }

    /**
     * @param string $contentUrl
     */
    public function setContentUrl(string $contentUrl): void
    {
        $this->contentUrl = $contentUrl;
    }

    /**
     * @param File $file
     */
    public function setFile(File $file): void
    {
        $this->file = $file;
        $this->format = $file->getMimetype();
        $this->size = $file->getSize();
    }
}
