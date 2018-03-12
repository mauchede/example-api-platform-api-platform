<?php

declare(strict_types=1);

namespace App\Bridge\ApiPlatform\EventListener;

use ApiPlatform\Core\Api\IriConverterInterface;
use ApiPlatform\Core\Exception\InvalidArgumentException;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;

final class HttpCacheDoctrineListener
{
    /**
     * @var TagAwareAdapterInterface
     */
    private $cache;

    /**
     * @var string
     */
    private $cacheKeyAlgo;

    /**
     * @var IriConverterInterface
     */
    private $iriConverter;

    public function __construct(TagAwareAdapterInterface $cache, string $cacheKeyAlgo, IriConverterInterface $iriConverter)
    {
        $this->cache = $cache;
        $this->cacheKeyAlgo = $cacheKeyAlgo;
        $this->iriConverter = $iriConverter;
    }

    public function postPersist(LifecycleEventArgs $event): void
    {
        $entity = $event->getEntity();

        $iris = [];
        try {
            $iris[] = $this->iriConverter->getIriFromResourceClass(\get_class($entity));
        } catch (InvalidArgumentException $exception) {
        }

        $this->cache->invalidateTags($this->getCacheTags($iris));
    }

    public function postRemove(LifecycleEventArgs $event): void
    {
        $entity = $event->getEntity();

        $iris = [];
        try {
            $iris[] = $this->iriConverter->getIriFromResourceClass(\get_class($entity));
            $iris[] = $this->iriConverter->getIriFromItem($entity);
        } catch (InvalidArgumentException $exception) {
        }

        $this->cache->invalidateTags($this->getCacheTags($iris));
    }

    public function postUpdate(LifecycleEventArgs $event): void
    {
        $entity = $event->getEntity();

        $iris = [];
        try {
            $iris[] = $this->iriConverter->getIriFromItem($entity);
        } catch (InvalidArgumentException $exception) {
        }

        $this->cache->invalidateTags($this->getCacheTags($iris));
    }

    private function getCacheTags(array $iris): array
    {
        return \array_map(
            function (string $iri): string {
                return \hash($this->cacheKeyAlgo, $iri);
            },
            $iris
        );
    }
}
