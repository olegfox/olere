<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Uploader;

use Gaufrette\Filesystem;
use Sylius\Bundle\CoreBundle\Model\ImageInterface;

class ImageUploader implements ImageUploaderInterface
{
    protected $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function upload(ImageInterface $image)
    {
        if (!$image->hasFile()) {
            return;
        }

        if (null !== $image->getPath()) {
            $this->remove($image->getPath());
        }

        do {
            $hash = md5(uniqid(mt_rand(), true));
            if(method_exists($image->getFile(), "getClientOriginalName")) {
                $path = $this->expandPath($hash . '.' . pathinfo((string)$image->getFile()->getClientOriginalName(), PATHINFO_EXTENSION));
            } elseif(method_exists($image->getFile(), "guessExtension")){
                $path = $this->expandPath($hash.'.'.$image->getFile()->guessExtension());
            }else{
                $path =  $this->expandPath($hash.'.'.pathinfo((string)$image->getFile(), PATHINFO_EXTENSION));
            }

        } while ($this->filesystem->has($path));

        $image->setPath($path);

        $this->filesystem->write(
            $image->getPath(),
            file_get_contents($image->getFile()->getPathname())
        );
    }

    public function upload2(ImageInterface $image)
    {
        if (!$image->hasFile2()) {
            return;
        }

        if (null !== $image->getPath2()) {
            $this->remove($image->getPath2());
        }

        do {
            $hash = md5(uniqid(mt_rand(), true));
            if(method_exists($image->getFile2(), "getClientOriginalName")) {
                $path = $this->expandPath($hash . '.' . pathinfo((string)$image->getFile2()->getClientOriginalName(), PATHINFO_EXTENSION));
            } elseif(method_exists($image->getFile2(), "guessExtension")){
                $path = $this->expandPath($hash.'.'.$image->getFile2()->guessExtension());
            }else{
                $path =  $this->expandPath($hash.'.'.pathinfo((string)$image->getFile2(), PATHINFO_EXTENSION));
            }

        } while ($this->filesystem->has($path));

        $image->setPath2($path);

        $this->filesystem->write(
            $image->getPath2(),
            file_get_contents($image->getFile2()->getPathname())
        );
    }

    public function remove($path)
    {
        return $this->filesystem->delete($path);
    }

    private function expandPath($path)
    {
        return sprintf(
            '%s/%s/%s',
            substr($path, 0, 2),
            substr($path, 2, 2),
            substr($path, 4)
        );
    }
}
