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

namespace rakelley\jhframe\classes;

use \rakelley\jhframe\interfaces\ITakesParameters;
use \rakelley\jhframe\interfaces\action\IHasResult;
use \rakelley\jhframe\interfaces\action\IRequiresValidation;
use \Psr\Log\LogLevel;

/**
 * Default implementation of IActionController
 */
class ActionController implements
    \rakelley\jhframe\interfaces\services\IActionController
{
    use \rakelley\jhframe\traits\LogsExceptions,
        \rakelley\jhframe\traits\ServiceLocatorAware;

    /**
     * Cache instance
     * @var object;
     */
    protected $cache;
    /**
     * ActionResult resource instance
     * @var object
     */
    protected $container;


    function __construct(
        \rakelley\jhframe\classes\resources\ActionResult $container,
        \rakelley\jhframe\interfaces\services\IKeyValCache $cache
    ) {
        $this->container = $container;
        $this->cache = $cache;
    }


    /**
     * @see \rakelley\jhframe\interfaces\services\IActionController::executeAction
     */
    public function executeAction($actionName, array $parameters=null)
    {
        $container = $this->container->getNewInstance();
        $action = $this->getLocator()->Make($actionName);

        if ($parameters && $action instanceof ITakesParameters) {
            $action->setParameters($parameters);
        }

        if ($action instanceof IRequiresValidation) {
            try {
                $valid = $action->Validate();
            } catch (InputException $e) {
                $this->logException($e, LogLevel::WARNING);
                $container->setError($e->getMessage());
                $valid = false;
            } catch (\Exception $e) {
                $this->logException($e, LogLevel::CRITICAL);
                $container->setError('An Internal Error Occurred');
                $valid = false;
            }
        } else {
            $valid = true;
        }

        if ($valid) {
            $success = $action->Proceed();
            //fallback if Proceed doesn't provide a return value
            if (!isset($success)) {
                $success = !$action->getError();
            }
        } else {
            $success = false;
        }
        $container->setSuccess($success);

        if ($success) {
            if ($action instanceof IHasResult) {
                $container->setMessage($action->getResult());
            }
            if ($action->touchesData()) {
                $this->cache->Purge();
            }
        } elseif (!$container->getError()) {
            $container->setError($action->getError());
        }

        return $container;
    }
}
