<?php

namespace App\EventListener;

use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class SlugListener
{
    private SluggerInterface $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    /**
     * @param LifecycleEventArgs<EntityManagerInterface> $args
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
        $this->handleSlug($args->getObject());
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        /** @var object $entity */
        $entity = $args->getObject();

        if (!method_exists($entity, 'getName') || !method_exists($entity, 'setSlug')) {
            return;
        }

        $name = $entity->getName();

        if (!$name) {
            return;
        }

        $slug = $this->slugger->slug($name)->lower();
        $entity->setSlug($slug);

        // IMPORTANT : on force Doctrine à voir la modification
        $em = $args->getObjectManager();
        $className = get_class($entity);
        /** @var class-string<object> $className */
        $meta = $em->getClassMetadata($className);
        $em->getUnitOfWork()->recomputeSingleEntityChangeSet($meta, $entity);
    }

    private function handleSlug(object $entity): void
    {
        if (!method_exists($entity, 'getName') || !method_exists($entity, 'setSlug')) {
            return;
        }

        $name = $entity->getName();

        if (!$name) {
            return;
        }

        $slug = $this->slugger->slug($name)->lower();
        $entity->setSlug($slug);
    }
}
