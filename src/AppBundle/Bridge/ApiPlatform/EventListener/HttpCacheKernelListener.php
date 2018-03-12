<?php

declare(strict_types=1);

namespace AppBundle\Bridge\ApiPlatform\EventListener;

use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

final class HttpCacheKernelListener
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
     * @var array
     */
    private $vary;

    public function __construct(TagAwareAdapterInterface $cache, string $cacheKeyAlgo, array $vary)
    {
        $this->cache = $cache;
        $this->cacheKeyAlgo = $cacheKeyAlgo;
        $this->vary = $vary;
    }

    public function onKernelRequest(GetResponseEvent $event): void
    {
        $request = $event->getRequest();
        if (!$event->isMasterRequest()) {
            return;
        }

        $item = $this->cache->getItem($this->getCacheKey($request));
        if ($item->isHit()) {
            $event->setResponse($item->get());
            $event->stopPropagation();
        }
    }

    public function onKernelResponse(FilterResponseEvent $event): void
    {
        $request = $event->getRequest();
        $response = $event->getResponse();

        $item = $this->cache->getItem($this->getCacheKey($request));
        if (!$item->isHit() && $this->isCacheableRequest($request) && $this->isCacheableResponse($response)) {
            $item->tag($this->getCacheTags($request));
            $item->set($event->getResponse());
            $this->cache->save($item);
        }
    }

    private function getCacheKey(Request $request): string
    {
        $data = [
            'method' => $request->getMethod(),
            'uri' => $request->getRequestUri(),
        ];
        foreach ($this->vary as $headerName) {
            $data[$headerName] = $request->headers->get($headerName);
        }

        return \hash($this->cacheKeyAlgo, \json_encode($data));
    }

    private function getCacheTags(Request $request): array
    {
        return \array_map(
            function (string $iri): string {
                return \hash($this->cacheKeyAlgo, $iri);
            },
            \array_unique(
                \array_merge(
                    [
                        $request->attributes->get('_api_normalization_context', [])['request_uri'],
                    ],
                    \array_values($request->attributes->get('_resources', []))
                )
            )
        );
    }

    private function isCacheableRequest(Request $request): bool
    {
        return $request->isMethodCacheable() && $request->attributes->has('_api_resource_class') && $request->attributes->get('_api_respond', false);
    }

    private function isCacheableResponse(Response $response): bool
    {
        return $response->isSuccessful();
    }
}
