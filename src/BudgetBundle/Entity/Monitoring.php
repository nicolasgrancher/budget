<?php
/**
 * Copyright (c) Nicolas Grancher <https://github.com/nicolasgrancher> 2017.
 */

namespace BudgetBundle\Entity;

/**
 * Class Monitoring
 * @package BudgetBundle\Entity
 */
class Monitoring
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $categories;
    /**
     * @var \DateTime
     */
    private $beginningDate;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->categories = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Monitoring
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Add category
     *
     * @param \BudgetBundle\Entity\Category $category
     *
     * @return Monitoring
     */
    public function addCategory(\BudgetBundle\Entity\Category $category)
    {
        $this->categories[] = $category;

        return $this;
    }

    /**
     * Remove category
     *
     * @param \BudgetBundle\Entity\Category $category
     */
    public function removeCategory(\BudgetBundle\Entity\Category $category)
    {
        $this->categories->removeElement($category);
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Get beginningDate
     *
     * @return \DateTime
     */
    public function getBeginningDate()
    {
        return $this->beginningDate;
    }

    /**
     * Set beginningDate
     *
     * @param \DateTime $beginningDate
     *
     * @return Monitoring
     */
    public function setBeginningDate($beginningDate)
    {
        $this->beginningDate = $beginningDate;

        return $this;
    }
}
