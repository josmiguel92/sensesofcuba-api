<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use ErrorException;
use Symfony\Component\HttpFoundation\File\File;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SocImageRepository")
 * @Vich\Uploadable()
 * @ORM\HasLifecycleCallbacks()
 */
class SocImage implements TimestampableInterface
{
    use TimestampableTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
    * @Vich\UploadableField(mapping="images", fileNameProperty="imageName")
    */
    private ?File $imageFile;

    /**
     * @ORM\Column(type="string", length=180, nullable=true)
     */
    private ?string $imageName;

    public function getId(): ?int
    {
        return $this->id ?? null;
    }

    public function getImageName(): ?string
    {
        return $this->imageName ?? null;
    }

    public function setImageName(string $imageName = null): self
    {
        $this->imageName = $imageName;

        return $this;
    }

    /**
     * @return File
     */
    public function getImageFile(): ?File
    {
        return $this->imageFile ?? null;
    }

    /**
     * @param File|null $file
     * @return SocImage
     */
    public function setImageFile(?File $file = null): self
    {
        $this->imageFile = $file;
        if (null !== $file) {
            $this->updatedAt = new DateTimeImmutable();
        }
        return $this;
    }

    public function __toString(): string
    {
        return $this->imageName ?? '';
    }

    public function getThumbnailPath(): string
    {
        return 'thumbs/' . $this->imageName;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PostUpdate()
     */
    public function createCustomThumbnail(): ?bool
    {
        $uploadDir = __DIR__ . '/../../public/uploads/images/';
        if(isset($this->imageName))
            return $this->createThumbnail(
            $uploadDir . $this->imageName,
            $uploadDir . 'thumbs/' . $this->imageName,
            400
        );
        return false;
    }

    //TODO: create a Thumbnail service

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
        if(!file_exists(dirname($dest)))
            try {
                mkdir(dirname($dest));
            } catch (\Exception $e) {
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
