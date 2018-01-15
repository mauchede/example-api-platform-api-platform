<?php

declare(strict_types=1);

namespace App\Metadata\Property\Factory;

use ApiPlatform\Core\Metadata\Property\Factory\PropertyNameCollectionFactoryInterface;
use ApiPlatform\Core\Metadata\Property\PropertyNameCollection;
use App\Entity\Media;

class PropertyNameCollectionFactory implements PropertyNameCollectionFactoryInterface
{
    /**
     * @var PropertyNameCollectionFactoryInterface
     */
    private $decorated;

    /**
     * @param PropertyNameCollectionFactoryInterface $decorated
     */
    public function __construct(PropertyNameCollectionFactoryInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    /**
     * {@inheritdoc}
     */
    public function create(string $resourceClass, array $options = []): PropertyNameCollection
    {
        $properties = iterator_to_array($this->decorated->create($resourceClass, $options)->getIterator());

        if (is_subclass_of($resourceClass, Media::class)) {
            $properties = array_filter(
                $properties,
                function (string $property): bool {
                    return 'file' !== $property;
                }
            );
        }

        return new PropertyNameCollection($properties);
    }
}
