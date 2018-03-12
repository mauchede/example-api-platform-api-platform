<?php

declare(strict_types=1);

namespace App\Controller;

use App\Factory\ResourcePropertiesFactory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;

final class ResourceController extends Controller
{
    /**
     * @var ResourcePropertiesFactory
     */
    private $resourcePropertiesFactory;

    /**
     * @var UrlMatcherInterface
     */
    private $urlMatcher;

    public function __construct(ResourcePropertiesFactory $resourcePropertiesFactory, UrlMatcherInterface $urlMatcher)
    {
        $this->resourcePropertiesFactory = $resourcePropertiesFactory;
        $this->urlMatcher = $urlMatcher;
    }

    /**
     * @Route(
     *     defaults={
     *         "_api_respond": true,
     *     },
     *     methods={
     *         "PROPFIND",
     *     },
     *     path="/{resource}",
     * )
     */
    public function properties(Request $request): array
    {
        $resourceClass = $this->extractResourceClass($request);
        if (null === $resourceClass) {
            throw $this->createNotFoundException();
        }

        return $this->resourcePropertiesFactory->create($resourceClass);
    }

    private function extractResourceClass(Request $request): ?string
    {
        $context = $this->urlMatcher->getContext();
        $context->setMethod(Request::METHOD_GET);

        try {
            $route = $this->urlMatcher->match($request->getPathInfo());

            return $route['_api_resource_class'];
        } catch (MethodNotAllowedException $exception) {
            return null;
        }
    }
}
