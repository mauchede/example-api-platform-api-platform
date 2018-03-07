<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use League\Flysystem\File;
use Symfony\Component\Serializer\Annotation as Serializer;

/**
 * @ApiResource(
 *     collectionOperations={},
 *     iri="http://schema.org/MediaObject",
 *     itemOperations={
 *         "content": {
 *             "route_name": "app_media_get_content",
 *             "swagger_context": {
 *                 "produces": {
 *                     "application/octet-stream",
 *                 },
 *                 "responses": {
 *                     "200": {
 *                         "description": "Content of Media",
 *                     },
 *                     "404": {
 *                         "description": "Resource not found",
 *                     },
 *                 },
 *                 "summary": "Retrieves content of a Media resource.",
 *             },
 *         }
 *     }
 * )
 *
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
     *     iri="http://schema.org/fileFormat",
     * )
     *
     * @ORM\Column(
     *     type="text",
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

    public function getContentUrl(): ?string
    {
        return $this->contentUrl;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function getFormat(): ?string
    {
        return $this->format;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setContentUrl(string $contentUrl): void
    {
        $this->contentUrl = $contentUrl;
    }

    public function setFile(File $file): void
    {
        $this->file = $file;
        $this->format = $file->getMimetype();
        $this->size = $file->getSize();
    }
}
