<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource(
 *     collectionOperations={
 *         "get",
 *         "post": {
 *             "route_name": "app_images_post_collection",
 *             "swagger_context": {
 *                 "consumes": {
 *                     "application/octet-stream",
 *                 },
 *                 "parameters": {},
 *             },
 *         }
 *     },
 *     iri="http://schema.org/ImageObject",
 *     itemOperations={
 *         "delete",
 *         "get",
 *     },
 * )
 *
 * @ORM\Entity
 */
class Image extends Media
{
}
