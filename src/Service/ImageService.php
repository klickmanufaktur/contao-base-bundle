<?php

namespace Klickmanufaktur\ContaoBaseBundle\Service;

use Contao\FilesModel;
use Contao\Image\ResizeConfiguration;
use Contao\System;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ImageService {

    private ContainerInterface $container;
    private string $projectDir;
    private object $imageFactory;

    public function __construct(
        //protected ImageFactoryInterface $imageFactory,
    ) {
        $this->container = System::getContainer();
        $this->projectDir = $this->container->getParameter('kernel.project_dir');
        $this->imageFactory = $this->container->get('contao.image.factory');
    }

    /**
     * @param $mode
     * @return string
     */
    public function getMode($mode): string
    {
        if($mode === 'crop') return ResizeConfiguration::MODE_CROP;
        return ResizeConfiguration::MODE_BOX;
    }

    /**
     * @param $imagePk
     * @param int $width
     * @param int $height
     * @param string $mode
     * @return string
     */
    public function getPath($imagePk, int $width = 1500, int $height = 1000, string $mode = 'crop'): string
    {
        $image = $this->imageFactory->create(
            FilesModel::findByPk($imagePk)->getAbsolutePath(),
            (new ResizeConfiguration())
                ->setWidth($width)
                ->setHeight($height)
                ->setMode($this->getMode($mode)),
            (new ResizeOptions())
                ->setImagineOptions(['format' => Format::ID_WEBP])
        );

        /*$image = $this->imageFactory->create(
            FilesModel::findByPk($imagePk)->getAbsolutePath(),
            [ $width, $height, $this->getMode($mode) ]
        );*/

        return $image->getUrl($this->projectDir);
    }

    /**
     * @param $imagePk
     * @return string
     */
    public function getAbsolutePath($imagePk): string
    {
        return FilesModel::findByPk($imagePk)->getAbsolutePath();
    }

    /**
     * @param $imagePk
     * @return array{author: mixed|string, license: mixed|string}
     */
    public function getMeta($imagePk): array|null
    {
        $fileModel = FilesModel::findByPk($imagePk);
        $readerWriter = $this->container->get('contao.image.metadata');
        $metadata = $readerWriter->toReadable($readerWriter->parse($this->projectDir . '/' . $fileModel->path));

        $fileMeta = unserialize($fileModel->meta);
        if(isset($fileMeta['de'])) {
            $copyrightArr = $fileMeta['de'];
            $copyrightArr['copyright'] = $fileMeta['de']['copyright'] ?? ($metadata['xmp']['pur:creditLine'][0] ?? '');
        }

        return $copyrightArr ?? null;
    }

    public function generate($imagePk, int $width = 1500, int $height = 1000, string $mode = 'crop'): array {
        return [
            'path' => $this->getPath($imagePk, $width, $height, $mode),
            'absolutePath' => $this->getAbsolutePath($imagePk),
            'meta' => $this->getMeta($imagePk),
        ];
    }

    public static function resizeByPath($path, int $width = 1500, int $height = 1000, string $mode = 'crop'): object {
        $instance = new self();

        $image = $instance->imageFactory->create(
            FilesModel::findByPk($imagePk)->getAbsolutePath(),
            (new ResizeConfiguration())
                ->setWidth($width)
                ->setHeight($height)
                ->setMode($instance->getMode($mode)),
            (new ResizeOptions())
                ->setImagineOptions(['format' => Format::ID_WEBP])
        );

        /*$image = $instance->imageFactory->create(
            FilesModel::findByPath($path)->getAbsolutePath(),
            [$width, $height, $instance->getMode($mode)]
        );*/

        $fileModel = FilesModel::findByPath($path);
        $readerWriter = $instance->container->get('contao.image.metadata');
        $metadata = $readerWriter->toReadable($readerWriter->parse($instance->projectDir . '/' . $fileModel->path));
        $fileMeta = unserialize($fileModel->meta);

        if(isset($fileMeta['de'])) {
            $copyrightArr = $fileMeta['de'];
            $copyrightArr['copyright'] = $fileMeta['de']['copyright'] ?? ($metadata['xmp']['pur:creditLine'][0] ?? '');
        }

        return (object) [
            'originalPath' => $path,
            'path' => $image->getUrl($instance->projectDir),
            'meta' => $copyrightArr ?? null,
        ];
    }

    public static function resizeByPk($pk, int $width = 1500, int $height = 1000, string $mode = 'crop'): object {
        $instance = new self();

        $image = $instance->imageFactory->create(
            FilesModel::findByPk($imagePk)->getAbsolutePath(),
            (new ResizeConfiguration())
                ->setWidth($width)
                ->setHeight($height)
                ->setMode($instance->getMode($mode)),
            (new ResizeOptions())
                ->setImagineOptions(['format' => Format::ID_WEBP])
        );

        /*$image = $instance->imageFactory->create(
            FilesModel::findByPk($pk)->getAbsolutePath(),
            [$width, $height, $instance->getMode($mode)]
        );*/

        $fileModel = FilesModel::findByPk($pk);
        $readerWriter = $instance->container->get('contao.image.metadata');
        $metadata = $readerWriter->toReadable($readerWriter->parse($instance->projectDir . '/' . $fileModel->path));
        $fileMeta = unserialize($fileModel->meta);

        $copyrightArr = [];

        if(isset($fileMeta['de'])) {
            $copyrightArr = $fileMeta['de'];
            $copyrightArr['copyright'] = $fileMeta['de']['copyright'] ?? ($metadata['xmp']['pur:creditLine'][0] ?? '');
        }

        return (object) [
            'originalPath' => FilesModel::findByPk($pk)->path,
            'path' => $image->getUrl($instance->projectDir),
            'meta' => (object) $copyrightArr ?? null,
        ];
    }

}