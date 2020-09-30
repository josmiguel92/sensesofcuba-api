<?php

namespace App;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Exception\MappingNotFoundException;
use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Mapping\PropertyMappingFactory;
use Vich\UploaderBundle\Storage\StorageInterface;
use Vich\UploaderBundle\Storage\FileSystemStorage;

/**
 * FileSystemStorage.
 *
 * @author Dustin Dobervich <ddobervich@gmail.com>
 */
class CustomStorage extends FileSystemStorage
{
    /**
     * @var PropertyMappingFactory
     */
    protected $factory;

    public function __construct(PropertyMappingFactory $factory)
    {
        $this->factory = $factory;
    }

    protected function doUpload(PropertyMapping $mapping, UploadedFile $file, ?string $dir, string $name)
    {
        $uploadDir = $mapping->getUploadDestination() . \DIRECTORY_SEPARATOR . $dir;

        return $file->move($uploadDir, $name);
    }

    public function upload($obj, PropertyMapping $mapping): void
    {
        $file = $mapping->getFile($obj);

        if (null === $file || !($file instanceof UploadedFile)) {
            throw new \LogicException('No uploadable file found');
        }

        $name = $mapping->getUploadName($obj);
        $mapping->setFileName($obj, $name);

        $mapping->writeProperty($obj, 'size', $file->getSize());
        $mapping->writeProperty($obj, 'mimeType', $file->getClientMimeType());
        $mapping->writeProperty($obj, 'originalName', $file->getClientOriginalName());

        if (false !== \strpos($file->getClientMimeType(), 'image/') && 'image/svg+xml' !== $file->getClientMimeType() && false !== $dimensions = @\getimagesize($file)) {
            $mapping->writeProperty($obj, 'dimensions', \array_splice($dimensions, 0, 2));
        }

        $dir = $mapping->getUploadDir($obj);

        $this->doUpload($mapping, $file, $dir, $name);
    }

    protected function doRemove(PropertyMapping $mapping, ?string $dir, string $name): ?bool
    {
        $file = $this->doResolvePath($mapping, $dir, $name);

        return \file_exists($file) ? \unlink($file) : false;
    }

    public function remove($obj, PropertyMapping $mapping): ?bool
    {
        $name = $mapping->getFileName($obj);

        if (empty($name)) {
            return false;
        }

        return $this->doRemove($mapping, $mapping->getUploadDir($obj), $name);
    }

    protected function doResolvePath(PropertyMapping $mapping, ?string $dir, string $name, ?bool $relative = false): string
    {
        $path = !empty($dir) ? $dir . \DIRECTORY_SEPARATOR . $name : $name;

        if ($relative) {
            return $path;
        }

        return $mapping->getUploadDestination() . \DIRECTORY_SEPARATOR . $path;
    }

    public function resolvePath($obj, ?string $fieldName = null, ?string $className = null, ?bool $relative = false): ?string
    {
        [$mapping, $filename] = $this->getFilename($obj, $fieldName, $className);

        if (empty($filename)) {
            return null;
        }

        return $this->doResolvePath($mapping, $mapping->getUploadDir($obj), $filename, $relative);
    }

    public function resolveUri($obj, ?string $fieldName = null, ?string $className = null): ?string
    {
        [$mapping, $filename] = $this->getFilename($obj, $fieldName, $className);

        if (empty($filename)) {
            return null;
        }

        $dir = $mapping->getUploadDir($obj);
        $path = !empty($dir) ? $dir . '/' . $filename : $filename;

        return $mapping->getUriPrefix() . '/' . $path;
    }

    public function resolveStream($obj, string $fieldName, ?string $className = null)
    {
        $path = $this->resolvePath($obj, $fieldName, $className);

        if (empty($path)) {
            return null;
        }

        return \fopen($path, 'rb');
    }

    /**
     * note: extension point.
     *
     * @param $obj
     *
     * @throws MappingNotFoundException
     * @throws \RuntimeException
     * @throws \Vich\UploaderBundle\Exception\NotUploadableException
     */
    protected function getFilename($obj, ?string $fieldName = null, ?string $className = null): array
    {
        $mapping = null === $fieldName ?
            $this->factory->fromFirstField($obj, $className) :
            $this->factory->fromField($obj, $fieldName, $className)
        ;

        if (null === $mapping) {
            throw new MappingNotFoundException(\sprintf('Mapping not found for field "%s"', $fieldName));
        }

        return [$mapping, $mapping->getFileName($obj)];
    }
}
