<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\classes;

/**
 * View type which constructs flat view files with no classes and little or no
 * inline logic.
 *
 * Basic functionality is all present but path to view files is set by namespace
 * of extending class, so empty extends are fine.
 */
class FlatView extends View implements
    \rakelley\jhframe\interfaces\ITakesParameters
{
    use \rakelley\jhframe\traits\ConfigAware;

    /**
     * Path to view file, set by ::mapNameToFile
     * @var string
     */
    protected $file;
    /**
     * FileSystemAbstractor instance
     * @var object
     */
    protected $fileSystem;
    /**
     * Absolute path to project directory from configstore
     * @var string
     */
    protected $rootDir;


    /**
     * @param \rakelley\jhframe\interfaces\services\IFileSystemAbstractor $fileSystem
     */
    function __construct(
        \rakelley\jhframe\interfaces\services\IFileSystemAbstractor $fileSystem
    ) {
        $this->rootDir = $this->getConfig()->Get('ENV', 'root_dir');

        $this->fileSystem = $fileSystem;
    }


    /**
     * {@inheritdoc}
     * @see \rakelley\jhframe\interfaces\ITakesParameters::setParameters()
     * @throws \BadMethodCallException if no 'view' parameter
     * @throws \DomainException if view file not found
     */
    public function setParameters(array $parameters=null)
    {
        if (!isset($parameters['view']) || !isset($parameters['namespace'])) {
            throw new \BadMethodCallException(
                'Required View argument Not Provided',
                500
            );
        }
        $this->file = $this->mapNameToFile($parameters['view'],
                                           $parameters['namespace']);
    }


    /**
     * {@inheritdoc}
     * @see \rakelley\jhframe\classes\View::constructView()
     */
    public function constructView()
    {
        $this->viewContent = $this->fileSystem->containeredInclude($this->file);
    }


    /**
     * Converts a view name to a corresponding full file path
     * 
     * @param  string $name      View name
     * @param  string $namespace Namespace of view
     * @return string            Path to view file
     * @throws \DomainException if file does not exist at expected path
     */
    protected function mapNameToFile($name, $namespace)
    {
        $name = strtolower($name);
        $namespace = str_replace('\\', '/', $namespace);
        $path = $this->rootDir . 'src/' . $namespace . '/' . $name . '.html';

        if (!$this->fileSystem->Exists($path)) {
            throw new \DomainException('Page ' . $name . ' Not Found', 404);
        }

        return $path;
    }
}
