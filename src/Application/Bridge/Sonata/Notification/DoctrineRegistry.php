<?php

namespace Application\Bridge\Sonata\Notification;

use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
class DoctrineRegistry implements RegistryInterface
{
    /**
     * @var DocumentManager
     */
    protected $manager;

    /**
     * Constructor.
     *
     * @param DocumentManager $manager
     */
    public function __construct(DocumentManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultConnectionName()
    {
        throw new \LogicException();
    }

    /**
     * {@inheritdoc}
     */
    public function getConnection($name = null)
    {
        throw new \LogicException();
    }

    /**
     * {@inheritdoc}
     */
    public function getConnections()
    {
        throw new \LogicException();
    }

    /**
     * {@inheritdoc}
     */
    public function getConnectionNames()
    {
        throw new \LogicException();
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultManagerName()
    {
        throw new \LogicException();
    }

    /**
     * {@inheritdoc}
     */
    public function getManager($name = null)
    {
        return $this->manager;
    }

    /**
     * {@inheritdoc}
     */
    public function getManagers()
    {
        return [$this->manager];
    }

    /**
     * {@inheritdoc}
     */
    public function resetManager($name = null)
    {
        throw new \LogicException();
    }

    /**
     * {@inheritdoc}
     */
    public function getAliasNamespace($alias)
    {
        throw new \LogicException();
    }

    /**
     * {@inheritdoc}
     */
    public function getManagerNames()
    {
        throw new \LogicException();
    }

    /**
     * {@inheritdoc}
     */
    public function getRepository($persistentObject, $persistentManagerName = null)
    {
        throw new \LogicException();
    }

    /**
     * {@inheritdoc}
     */
    public function getManagerForClass($class)
    {
        throw new \LogicException();
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultEntityManagerName()
    {
        throw new \LogicException();
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityManager($name = null)
    {
        throw new \LogicException();
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityManagers()
    {
        throw new \LogicException();
    }

    /**
     * {@inheritdoc}
     */
    public function resetEntityManager($name = null)
    {
        throw new \LogicException();
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityNamespace($alias)
    {
        throw new \LogicException();
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityManagerNames()
    {
        throw new \LogicException();
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityManagerForClass($class)
    {
        throw new \LogicException();
    }
}
