<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FixturesBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Core\Model\ProductVariantImage;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Default product images.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class LoadImagesData extends DataFixture
{
    public function load(ObjectManager $manager)
    {
        $finder = new Finder();
        $uploader = $this->get('sylius.image_uploader');

        $path = __DIR__.'/../../Resources/fixtures';
        foreach ($finder->files()->in($path) as $img) {
            $image = new ProductVariantImage();
            $image->setFile(new UploadedFile($img->getRealPath(), $img->getFilename()));
            $uploader->upload($image);

            $manager->persist($image);

            $this->setReference('Sylius.Image.'.$img->getBasename('.jpg'), $image);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 5;
    }
}
