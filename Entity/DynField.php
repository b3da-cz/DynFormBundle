<?php

namespace b3da\DynFormBundle\Entity;

class DynField
{
    protected $id;

    # field name and type
    protected $DOMelementId;
    protected $type;

    # field options []
    protected $label;
    protected $required;
    protected $choices_as_values;
    protected $choices;
    protected $data;

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
     * Set dOMelementId
     *
     * @param string $dOMelementId
     *
     * @return DynField
     */
    public function setDOMelementId($dOMelementId)
    {
        $this->DOMelementId = $dOMelementId;

        return $this;
    }

    /**
     * Get dOMelementId
     *
     * @return string
     */
    public function getDOMelementId()
    {
        return $this->DOMelementId;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return DynField
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set label
     *
     * @param string $label
     *
     * @return DynField
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set required
     *
     * @param boolean $required
     *
     * @return DynField
     */
    public function setRequired($required)
    {
        $this->required = $required;

        return $this;
    }

    /**
     * Get required
     *
     * @return boolean
     */
    public function getRequired()
    {
        return $this->required;
    }

    /**
     * Set choicesAsValues
     *
     * @param string $choicesAsValues
     *
     * @return DynField
     */
    public function setChoicesAsValues($choicesAsValues)
    {
        $this->choices_as_values = $choicesAsValues;

        return $this;
    }

    /**
     * Get choicesAsValues
     *
     * @return string
     */
    public function getChoicesAsValues()
    {
        return $this->choices_as_values;
    }

    /**
     * Set choices
     *
     * @param array $choices
     *
     * @return DynField
     */
    public function setChoices($choices)
    {
        $this->choices = $choices;

        return $this;
    }

    /**
     * Get choices
     *
     * @return array
     */
    public function getChoices()
    {
        return $this->choices;
    }

    /**
     * Set data
     *
     * @param string $data
     *
     * @return DynField
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data
     *
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }
}
