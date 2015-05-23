<?php
/**
 * @package jhframe
 * 
 * All content covered under The MIT License except where included 3rd-party
 * vendor files are licensed otherwise.
 * 
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\traits\view;

/**
 * Default Trait implementation of \rakelley\jhframe\interfaces\view\IHasMetaData
 * for Views which need to fetch additional metadata from a repository.
 */
trait GetsMetaData
{

    /**
     * String route identifier provided to repository, classes employing
     * this trait should overwrite appropriately.
     * @var string
     */
    protected $metaRoute = null;
    protected $metaDataServiceInterface =
        '\rakelley\jhframe\interfaces\repository\IMetaData';


    /**
     * ServiceLocator dependency, can be resolved by using
     * \rakelley\jhframe\traits\ServiceLocatorAware
     * @abstract
     */
    abstract protected function getLocator();


    /**
     * ViewController called method to get data and set/merge class metaData
     * property
     * 
     * @return void
     */
    public function fetchMetaData()
    {
        if (!$this->metaRoute) return;

        $service = $this->getLocator()->Make($this->metaDataServiceInterface);
        $data = ($service->getPage($this->metaRoute)) ?: [];

        $this->metaData = (!empty($this->metaData)) ?
                          array_merge($data, $this->metaData) : $data;
    }
}
