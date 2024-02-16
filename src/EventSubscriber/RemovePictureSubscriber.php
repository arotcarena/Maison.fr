<?php

namespace App\EventSubscriber;

use App\Entity\Picture;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Vich\UploaderBundle\Storage\StorageInterface;

class RemovePictureSubscriber implements EventSubscriberInterface
{
    private CacheManager $cacheManager;

    private StorageInterface $storageInterface;

    public function __construct(CacheManager $cacheManager, StorageInterface $storageInterface)
    {
        $this->cacheManager = $cacheManager;
        $this->storageInterface = $storageInterface;
    }

    public function getSubscribedEvents()
    {
        return [
            Events::preRemove => 'preRemove'
        ];
    }

    /** 
     * Supprime les filtres en cache correspondant à la Picture supprimée
     */
    public function preRemove(LifecycleEventArgs $event)
    {
        if($event->getObject() instanceof Picture)
        {
            $path = $this->storageInterface->resolveUri($event->getObject(), 'imageFile');
            $this->cacheManager->remove($path, 'my_thumb');
        }
    }
}
