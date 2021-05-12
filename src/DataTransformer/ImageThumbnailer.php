<?php

namespace App\DataTransformer;

class ImageThumbnailer
{



    // Link image type to correct image loader and saver
    // - makes it easier to add additional types later on
    // - makes the function easier to read
    private const IMAGE_HANDLERS = [
        IMAGETYPE_JPEG => [
            'load' => 'imagecreatefromjpeg',
            'save' => 'imagejpeg',
            'quality' => 80
        ],
        IMAGETYPE_PNG => [
            'load' => 'imagecreatefrompng',
            'save' => 'imagepng',
            'quality' => 0
        ],
        IMAGETYPE_GIF => [
            'load' => 'imagecreatefromgif',
            'save' => 'imagegif'
        ]
    ];

    private string $imageName;
    private string $destinationPath;
    private string $uploadDir;
    private int $targetWidth;
    private ?int $targetHeight;

    /**
     * ImageThumbnailer constructor.
     */
    public function __construct(
        string $uploadDir,
        string $imageName,
        ?string $destinationPath = null,
        $targetWidth = 400,
        $targetHeight = null
    ) {
        $this->uploadDir = $uploadDir;
        $this->imageName = $imageName;
        $this->destinationPath = $destinationPath ?? $uploadDir . 'thumbs/' . $imageName;
        $this->targetWidth = $targetWidth;
        $this->targetHeight = $targetHeight;
    }

    public function makeThumb(): ?bool
    {
        return $this->createThumbnail(
            $this->uploadDir . $this->imageName,
            $this->destinationPath,
            $this->targetWidth,
            $this->targetHeight
        );
    }
    /**
     * @param $src - a valid file location
     * @param $dest - a valid file target
     * @param $targetWidth - desired output width
     * @param $targetHeight - desired output height or null
     * @return false|mixed|null
     * @return false|mixed|null
     */
    public function createThumbnail($src, $dest, $targetWidth, $targetHeight = null): ?bool
    {
        if (file_exists($src) === false || is_file($src) === false) {
            return false;
        }

        // 1. Load the image from the given $src
        // - see if the file actually exists
        // - check if it's of a valid image type
        // - load the image resource

        // get the type of the image
        // we need the type to determine the correct loader
        $type = function_exists('exif_imagetype') ? exif_imagetype($src) : null;


        // if no valid type or no handler found -> exit
        if (!$type || !self::IMAGE_HANDLERS[$type]) {
            return null;
        }

        // load the image with the correct loader
        $image = call_user_func(self::IMAGE_HANDLERS[$type]['load'], $src);

        // no image found at supplied location -> exit
        if (!$image) {
            return null;
        }


        // 2. Create a thumbnail and resize the loaded $image
        // - get the image dimensions
        // - define the output size appropriately
        // - create a thumbnail based on that size
        // - set alpha transparency for GIFs and PNGs
        // - draw the final thumbnail

        // get original image width and height
        $width = imagesx($image);
        $height = imagesy($image);

        // maintain aspect ratio when no height set
        if ($targetHeight == null) {
            // get width to height ratio
            $ratio = $width / $height;

            // if is portrait
            // use ratio to scale height to fit in square
            if ($width > $height) {
                $targetHeight = floor($targetWidth / $ratio);
            } else {
                // if is landscape
                // use ratio to scale width to fit in square

                $targetHeight = $targetWidth;
                $targetWidth = floor($targetWidth * $ratio);
            }
        }

        // create duplicate image based on calculated target size
        $thumbnail = imagecreatetruecolor($targetWidth, $targetHeight);

        // set transparency options for GIFs and PNGs
        if ($type == IMAGETYPE_GIF || $type == IMAGETYPE_PNG) {
            // make image transparent
            imagecolortransparent(
                $thumbnail,
                imagecolorallocate($thumbnail, 0, 0, 0)
            );

            // additional settings for PNGs
            if ($type == IMAGETYPE_PNG) {
                imagealphablending($thumbnail, false);
                imagesavealpha($thumbnail, true);
            }
        }

        // copy entire source image to duplicate image and resize
        imagecopyresampled(
            $thumbnail,
            $image,
            0,
            0,
            0,
            0,
            $targetWidth,
            $targetHeight,
            $width,
            $height
        );


        // 3. Save the $thumbnail to disk
        // - call the correct save method
        // - set the correct quality level

        // save the duplicate version of the image to disk
        if (!file_exists(dirname($dest))) {
            try {
                mkdir(dirname($dest));
            } catch (\Exception $e) {
            }
        }



        $code = null;
        try {
            $code = call_user_func(
                self::IMAGE_HANDLERS[$type]['save'],
                $thumbnail,
                $dest,
                self::IMAGE_HANDLERS[$type]['quality']
            );
        } catch (\Exception $e) {
        }
        return $code;
    }
}
