<?php

declare(strict_types=1);

namespace AppBundle\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource(
 *     collectionOperations={
 *         "get": {
 *             "method": "GET",
 *         },
 *         "post": {
 *             "route_name": "app_images_post_collection",
 *             "swagger_context": {
 *                 "consumes": {
 *                     "application/octet-stream"
 *                 },
 *                 "parameters": {},
 *             },
 *         }
 *     },
 *     iri="http://schema.org/ImageObject",
 *     itemOperations={
 *         "delete": {
 *             "method": "DELETE",
 *         },
 *         "get": {
 *             "method": "GET",
 *         },
 *     },
 * )
 *
 * @ORM\Entity
 */
class Image extends Media
{
}
