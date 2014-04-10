<?php
/**
 * This file is part of PhpORM
 *
 * @package PhpORM
 * @license http://opensource.org/licenses/BSD-3-Clause BSD
 */

namespace PhpORM\Repository;

/**
 * Abstract class to build repositories from
 * Sets up some sane defaults for how most repositories will access the storage and create objects.
 *
 * @package PhpORM
 */
class RepositoryAbstract implements RepositoryInterface
{
    /**
     * Storage system we're working against
     * @var object
     */
    protected $storage;

    /**
     * Constructor
     *
     * @param object $storage Storage mechanism to use
     * @param object $prototype Object prototype to build SQL results from
     */
    public function __construct($storage, $prototype)
    {
        $this->storage = $storage;
        $this->prototype = $prototype;
    }

    /**
     * Returns all of the objects in the storage container
     *
     * @return array
     */
    public function fetchAll()
    {
        $rowset = $this->storage->fetchAll($this->table);
        $class = get_class($this->prototype);

        $entities = array();
        foreach($rowset as $row) {
            $entity = new $class;
            foreach($row as $member => $value) {
                $entity->$member = $value;
            }
            $entities[]= $entity;
        }

        return $entities;
    }

    /**
     * Returns all of the objects that match a series of criteria.
     * The criteria is entered as an array, with the key the column name and the value the value in the DB.
     *
     * @param array $criteria
     * @return array
     */
    public function fetchAllBy($criteria)
    {
        $rowset = $this->storage->fetchAllBy($criteria, $this->table);
        $class = get_class($this->prototype);

        $entities = array();
        foreach($rowset as $row) {
            $entity = new $class;
            foreach($row as $member => $value) {
                $entity->$member = $value;
            }
            $entities[]= $entity;
        }

        return $entities;
    }

    /**
     * Return a single entry, searched on by the identifier column
     *
     * @param scalar $identifier
     * @return object|null
     */
    public function find($identifier)
    {
        $row = $this->storage->find(array($this->identifierColumn => $identifier), $this->table);

        if(!empty($row)) {
            $class = get_class($this->prototype);
            $entity = new $class;
            foreach($row as $member => $value) {
                $entity->$member = $value;
            }
            return $entity;
        }

        return null;
    }

    /**
     * Return a single entry, searched for by the criteria entered
     * Criteria must in in an array, with the column name the key and the DB value the value
     *
     * @param array $criteria
     * @return object|null
     */
    public function findBy($criteria)
    {
        $row = $this->storage->find($criteria, $this->table);

        if(!empty($row)) {
            $class = get_class($this->prototype);
            $entity = new $class;
            foreach($row as $member => $value) {
                $entity->$member = $value;
            }
            return $entity;
        }

        return null;
    }

    /**
     * Saves an object to the storage container
     * This will return the new identifier that was generated by the storage container. For example, with databases this
     * will return the insert ID.
     *
     * @param array $data
     * @return int
     */
    public function save($data)
    {
        return $this->storage->save($data, $this->table);
    }
}