<?php

declare(strict_types=1);

namespace AppBundle\Controller;

use AppBundle\Entity\Media;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

final class MediaController
{
    private const BUFFER_SIZE = 8192;

    /**
     * @Route(
     *     methods={
     *         "GET"
     *     },
     *     name="app_media_get_content",
     *     path="/media/{id}/content",
     * )
     *
     * @param Media $media
     *
     * @return StreamedResponse
     */
    public function content(Media $media): StreamedResponse
    {
        $file = $media->getFile();

        return new StreamedResponse(
            function () use ($file) {
                $stream = $file->readStream();
                while (!\feof($stream)) {
                    echo \fread($stream, self::BUFFER_SIZE);
                    \flush();
                }
            },
            StreamedResponse::HTTP_OK,
            [
                'Content-Type' => $file->getMimetype(),
            ]
        );
    }
}
