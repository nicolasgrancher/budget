<?php
/**
 * Copyright (c) Nicolas Grancher <https://github.com/nicolasgrancher> 2017.
 */

namespace BudgetBundle\Entity;

/**
 * Class OperationPattern
 * @package BudgetBundle\Entity
 */
class OperationPattern
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $pattern;

    /**
     * @var string
     */
    private $description;

    /**
     * @var \BudgetBundle\Entity\Category
     */
    private $category;


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
     * Get pattern
     *
     * @return string
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * Set pattern
     *
     * @param string $pattern
     *
     * @return OperationPattern
     */
    public function setPattern($pattern)
    {
        $this->pattern = $pattern;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return OperationPattern
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get category
     *
     * @return \BudgetBundle\Entity\Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set category
     *
     * @param \BudgetBundle\Entity\Category $category
     *
     * @return OperationPattern
     */
    public function setCategory(\BudgetBundle\Entity\Category $category = null)
    {
        $this->category = $category;

        return $this;
    }
}
