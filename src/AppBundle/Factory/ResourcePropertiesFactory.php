<?php

declare(strict_types=1);

namespace AppBundle\Factory;

use ApiPlatform\Core\Api\FilterInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use ApiPlatform\Core\Metadata\Resource\ResourceMetadata;
use Symfony\Component\DependencyInjection\ServiceLocator;

final class ResourcePropertiesFactory
{
    /**
     * @var ServiceLocator
     */
    private $filterLocator;

    /**
     * @var int
     */
    private $itemsPerPage;

    /**
     * @var ResourceMetadataFactoryInterface
     */
    private $resourceMetadataFactory;

    public function __construct(ServiceLocator $filterLocator, ResourceMetadataFactoryInterface $resourceMetadataFactory, int $itemsPerPage)
    {
        $this->filterLocator = $filterLocator;
        $this->itemsPerPage = $itemsPerPage;
        $this->resourceMetadataFactory = $resourceMetadataFactory;
    }

    public function create(string $resourceClass): array
    {
        $resourceProperties = [
            'fields' => [],
            'itemsPerPage' => $this->itemsPerPage,
            'sort' => [],
        ];

        $resourceMetadata = $this->resourceMetadataFactory->create($resourceClass);

        foreach ($resourceMetadata->getAttribute('order', []) as $field => $sort) {
            $resourceProperties['sort'][] = [
                'field' => $field,
                'sort' => $sort,
            ];
        }

        foreach ($this->extractFiltersFromResourceMetadata($resourceMetadata) as $filter) {
            switch (\get_class($filter)) {
                case OrderFilter::class:
                    foreach ($this->extractFieldsFromFilter($filter, $resourceClass) as $fieldName) {
                        $resourceProperties['fields'][$fieldName] = [
                            'name' => $fieldName,
                            'sortable' => true,
                        ];
                    }
                    break;
            }
        }

        $resourceProperties['fields'] = \array_values($resourceProperties['fields']);

        return $resourceProperties;
    }

    private function extractFieldsFromFilter(FilterInterface $filter, string $resourceClass): array
    {
        return \array_values(
            \array_map(
                function (array $documentation) {
                    return $documentation['property'];
                },
                $filter->getDescription($resourceClass)
            )
        );
    }

    private function extractFiltersFromResourceMetadata(ResourceMetadata $resourceMetadata): array
    {
        $filters = [];
        foreach ($resourceMetadata->getAttribute('filters', []) as $id) {
            $filters[] = $this->filterLocator->get($id);
        }

        return $filters;
    }
}
