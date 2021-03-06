<?php

namespace b3da\DynFormBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;

class DynForm
{
    protected $id;
    protected $name;

    # OneToMany: DynField
    protected $field;

    # OneToMany: DynFormResult
    protected $result;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->field = new ArrayCollection();
        $this->result = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return DynForm
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add field
     *
     * @param \b3da\DynFormBundle\Entity\DynField $field
     *
     * @return DynForm
     */
    public function addField(\b3da\DynFormBundle\Entity\DynField $field)
    {
        $this->field[] = $field;

        return $this;
    }

    /**
     * Remove field
     *
     * @param \b3da\DynFormBundle\Entity\DynField $field
     */
    public function removeField(\b3da\DynFormBundle\Entity\DynField $field)
    {
        $this->field->removeElement($field);
    }

    /**
     * Get field
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * Add result
     *
     * @param \b3da\DynFormBundle\Entity\DynFormResult $result
     *
     * @return DynForm
     */
    public function addResult(\b3da\DynFormBundle\Entity\DynFormResult $result)
    {
        $this->result[] = $result;

        return $this;
    }

    /**
     * Remove result
     *
     * @param \b3da\DynFormBundle\Entity\DynFormResult $result
     */
    public function removeResult(\b3da\DynFormBundle\Entity\DynFormResult $result)
    {
        $this->result->removeElement($result);
    }

    /**
     * Get result
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getResult()
    {
        return $this->result;
    }
}
