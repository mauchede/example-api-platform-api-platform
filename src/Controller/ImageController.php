<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Image;
use League\Flysystem\FileExistsException;
use League\Flysystem\FilesystemInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ImageController extends AbstractController
{
    /**
     * @var FilesystemInterface
     */
    private $filesystem;

    /**
     * @var RegistryInterface
     */
    private $registry;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @param FilesystemInterface $filesystem
     * @param RegistryInterface   $registry
     * @param ValidatorInterface  $validator
     */
    public function __construct(FilesystemInterface $filesystem, RegistryInterface $registry, ValidatorInterface $validator)
    {
        $this->filesystem = $filesystem;
        $this->registry = $registry;
        $this->validator = $validator;
    }

    /**
     * @Route(
     *     methods={"POST"},
     *     name="app_images_post_collection",
     *     path="/images",
     *     defaults={
     *         "_api_respond"=true
     *     }
     * )
     *
     * @param Request $request
     *
     * @throws FileExistsException
     *
     * @return ConstraintViolationListInterface|Image|JsonResponse
     */
    public function post(Request $request)
    {
        $file = $request->files->get('file');
        if (null === $file) {
            return new JsonResponse(JsonResponse::$statusTexts[JsonResponse::HTTP_BAD_REQUEST], JsonResponse::HTTP_BAD_REQUEST);
        }

        $violations = $this->validator->validate(
            $file,
            [
                new Assert\File(
                    [
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                    ]
                ),
            ]
        );
        if (0 !== $violations->count()) {
            return $violations;
        }

        $fileName = uniqid();
        $fileResource = fopen($file->getRealPath(), 'r');
        $this->filesystem->writeStream($fileName, $fileResource);
        fclose($fileResource);

        $image = new Image();
        $image->setFile($this->filesystem->get($fileName));

        $manager = $this->registry->getManager();
        $manager->persist($image);
        $manager->flush();

        return $image;
    }
}
