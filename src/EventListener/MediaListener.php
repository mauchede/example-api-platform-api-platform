<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Media;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use League\Flysystem\FileExistsException;
use League\Flysystem\FilesystemInterface;
use Symfony\Component\Routing\RouterInterface;

class MediaListener
{
    /**
     * @var FilesystemInterface
     */
    private $filesystem;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @param FilesystemInterface $filesystem
     * @param RouterInterface     $router
     */
    public function __construct(FilesystemInterface $filesystem, RouterInterface $router)
    {
        $this->filesystem = $filesystem;
        $this->router = $router;
    }

    /**
     * @param LifecycleEventArgs $event
     */
    public function postLoad(LifecycleEventArgs $event): void
    {
        $object = $event->getObject();
        if (!$object instanceof Media) {
            return;
        }

        $this->initializeContentUrl($object);
        $this->initializeFile($object);
    }

    /**
     * @param LifecycleEventArgs $event
     *
     * @throws FileExistsException
     */
    public function postPersist(LifecycleEventArgs $event): void
    {
        $object = $event->getObject();
        if (!$object instanceof Media) {
            return;
        }

        $this->moveIntoMediaFolder($object);
        $this->initializeContentUrl($object);
        $this->initializeFile($object);
    }

    /**
     * @param LifecycleEventArgs $event
     */
    public function postRemove(LifecycleEventArgs $event): void
    {
        $object = $event->getObject();
        if (!$object instanceof Media) {
            return;
        }

        $object->getFile()->delete();
    }

    /**
     * @param Media $media
     */
    private function initializeContentUrl(Media $media): void
    {
        $media->setContentUrl(
            $this->router->generate('app_media_get_content', ['id' => $media->getId()], RouterInterface::ABSOLUTE_URL)
        );
    }

    /**
     * @param Media $media
     */
    private function initializeFile(Media $media): void
    {
        $media->setFile($this->filesystem->get($media->getId()));
    }

    /**
     * @param Media $media
     *
     * @throws FileExistsException
     */
    private function moveIntoMediaFolder(Media $media): void
    {
        $file = $media->getFile();
        $this->filesystem->writeStream($media->getId(), $file->readStream());
        $file->delete();
    }
}
