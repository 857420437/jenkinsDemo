<?php

namespace Phalcon\Mvc;

use Phalcon\Application as BaseApplication;
use Phalcon\Http\ResponseInterface;
use Phalcon\Mvc\Application\Exception;

/**
 * Phalcon\Mvc\Application
 *
 * This component encapsulates all the complex operations behind instantiating every component
 * needed and integrating it with the rest to allow the MVC pattern to operate as desired.
 *
 * <code>
 *
 * class Application extends \Phalcon\Mvc\Application
 * {
 *      /\**
 *       * Register the services here to make them general or register
 *       * in the ModuleDefinition to make them module-specific
 *       *\/
 *      protected function _registerServices()
 *      {
 *
 *      }
 *
 *      /\**
 *       * This method registers all the modules in the application
 *       *\/
 *      public function main()
 *      {
 *          $this->registerModules(array(
 *              'frontend' => array(
 *                  'className' => 'Multiple\Frontend\Module',
 *                  'path' => '../apps/frontend/Module.php'
 *              ),
 *              'backend' => array(
 *                  'className' => 'Multiple\Backend\Module',
 *                  'path' => '../apps/backend/Module.php'
 *              )
 *          ));
 *      }
 *  }
 *
 *  $application = new Application();
 *  $application->main();
 *
 * </code>
 *
 * @see https://github.com/phalcon/cphalcon/blob/1.2.6/ext/mvc/application.c
 */
class Application extends BaseApplication
{

	protected $_implicitView = true;

	/**
	 * By default. The view is implicitly buffering all the output
	 * You can full disable the view component using this method
	 * @param Application $implicitView
	 * @return Application
	 */
	public function useImplicitView($implicitView)
	{
		$this->_implicitView = $implicitView;
		return $this;
	}

	/**
	 * Handles a MVC request
	 * @param string $uri
	 * @return ResponseInterface|boolean
	 * @throws \Exception
	 */
	public function handle($uri = null)
	{

		$dependencyInjector = $this->_dependencyInjector;

		if (!is_object($dependencyInjector)) {
			throw new Exception("A dependency injection object is required to access internal services");
		}

		$eventsManager = $this->_eventsManager;

		/**
		 * Call boot event, this allow the developer to perform initialization actions
		 */
		if (is_object($eventsManager)) {
			if ($eventsManager->fire("application:boot", $this) === false) {
				return false;
			}
		}

		$router = $dependencyInjector->getShared("router");

		/**
		 * Handle the URI pattern (if any)
		 */
		$router->handle($uri);

		/**
		 * If a 'match' callback was defined in the matched route
		 * The whole dispatcher+view behavior can be overridden by the developer
		 */
		$matchedRoute = $router->getMatchedRoute();
		if (is_object($matchedRoute)) {
			$match = $matchedRoute->getMatch();
			if ($match !== null) {

				if ($match instanceof \Closure) {
					$match = \Closure::bind($match, $dependencyInjector);
				}

				/**
				 * Directly call the match callback
				 */
				$possibleResponse = call_user_func_array($match, $router->getParams());

				/**
				 * If the returned value is a string return it as body
				 */
				if (is_string($possibleResponse)) {
					$response = $dependencyInjector->getShared("response");
					$response->setContent($possibleResponse);
					return $response;
				}

				/**
				 * If the returned string is a ResponseInterface use it as response
				 */
				if (is_object($possibleResponse)) {
					if ($possibleResponse instanceof ResponseInterface) {
						$possibleResponse->sendHeaders();
						$possibleResponse->sendCookies();
						return $possibleResponse;
					}
				}
			}
		}

		/**
		 * If the router doesn't return a valid module we use the default module
		 */
		$moduleName = $router->getModuleName();
		if (!$moduleName) {
			$moduleName = $this->_defaultModule;
		}

		$moduleObject = null;

		/**
		 * Process the module definition
		 */
		if ($moduleName) {

			if (is_object($eventsManager)) {
				if ($eventsManager->fire("application:beforeStartModule", $this, $moduleName) === false) {
					return false;
				}
			}

			/**
			 * Gets the module definition
			 */
			$module = $this->getModule($moduleName);

			/**
			 * A module definition must ne an array or an object
			 */
			if (!is_array($module) && !is_object($module)) {
				throw new Exception("Invalid module definition");
			}

			/**
			 * An array module definition contains a path to a module definition class
			 */
			if (is_array($module)) {

				/**
				 * Class name used to load the module definition
				 */
				$className = $module["className"];

				if (!$className) {
					$className = "Module";
				}

				/**
				 * If developer specify a path try to include the file
				 */
				$path = $module["path"];

				if ($path) {
					if (!class_exists($className, false)) {
						if (!file_exists($path)) {
							throw new Exception("Module definition path '" . $path . "' doesn't exist");
						}

						require $path;
					}
				}

				$moduleObject = $dependencyInjector->get($className);

				/**
				 * 'registerAutoloaders' and 'registerServices' are automatically called
				 */
				$moduleObject->registerAutoloaders($dependencyInjector);
				$moduleObject->registerServices($dependencyInjector);

			} else {
				/**
				 * A module definition object, can be a Closure instance
				 */
				if (!$module instanceof \Closure) {
					throw new Exception("Invalid module definition");
				}

				$moduleObject = call_user_func_array($module, array($dependencyInjector));
			}

			/**
			 * Calling afterStartModule event
			 */
			if (is_object($eventsManager)) {
				$eventsManager->fire("application:afterStartModule", $this, $moduleObject);
			}
		}

		/**
		 * Check whether use implicit views or not
		 */
		$implicitView = $this->_implicitView;

		if ($implicitView === true) {
			$view = $dependencyInjector->getShared("view");
		}

		/**
		 * We get the parameters from the router and assign them to the dispatcher
		 * Assign the values passed from the router
		 */
		$dispatcher = $dependencyInjector->getShared("dispatcher");
		$dispatcher->setModuleName($router->getModuleName());
		$dispatcher->setNamespaceName($router->getNamespaceName());
		$dispatcher->setControllerName($router->getControllerName());
		$dispatcher->setActionName($router->getActionName());
		$dispatcher->setParams($router->getParams());

		/**
		 * Start the view component (start output buffering)
		 */
		if ($implicitView === true) {
			$view->start();
		}

		/**
		 * Calling beforeHandleRequest
		 */
		if (is_object($eventsManager)) {
			if ($eventsManager->fire("application:beforeHandleRequest", $this, $dispatcher) === false) {
				return false;
			}
		}

		/**
		 * The dispatcher must return an object
		 */
		$controller = $dispatcher->dispatch();

		/**
		 * Get the latest value returned by an action
		 */
		$possibleResponse = $dispatcher->getReturnedValue();

		/**
		 * Returning false from an action cancels the view
		 */
		if (is_bool($possibleResponse) && $possibleResponse === false) {
			$response = $dependencyInjector->getShared("response");
		} else {

			/**
			 * Returning a string makes use it as the body of the response
			 */
			if (is_string($possibleResponse)) {
				$response = $dependencyInjector->getShared("response");
				$response->setContent($possibleResponse);
			} else {

				/**
				 * Check if the returned object is already a response
				 */
				$returnedResponse = ((is_object($possibleResponse)) && ($possibleResponse instanceof ResponseInterface));

				/**
				 * Calling afterHandleRequest
				 */
				if (is_object($eventsManager)) {
					$eventsManager->fire("application:afterHandleRequest", $this, $controller);
				}

				/**
				 * If the dispatcher returns an object we try to render the view in auto-rendering mode
				 */
				if ($returnedResponse === false && $implicitView === true) {
					if (is_object($controller)) {
						$renderStatus = true;

						/**
						 * This allows to make a custom view render
						 */
						if (is_object($eventsManager)) {
							$renderStatus = $eventsManager->fire("application:viewRender", $this, $view);
						}

						/**
						 * Check if the view process has been treated by the developer
						 */
						if ($renderStatus !== false) {

							/**
							 * Automatic render based on the latest controller executed
							 */
							$view->render(
								$dispatcher->getControllerName(),
								$dispatcher->getActionName()
							);
						}
					}
				}

				/**
				 * Finish the view component (stop output buffering)
				 */
				if ($implicitView === true) {
					$view->finish();
				}

				if ($returnedResponse === true) {

					/**
					 * We don't need to create a response because there is one already created
					 */
					$response = $possibleResponse;
				} else {

					$response = $dependencyInjector->getShared("response");
					if ($implicitView === true) {
						/**
						 * The content returned by the view is passed to the response service
						 */
						$response->setContent($view->getContent());
					}
				}
			}
		}

		/**
		 * Calling beforeSendResponse
		 */
		if (is_object($eventsManager)) {
			$eventsManager->fire("application:beforeSendResponse", $this, $response);
		}

		/**
		 * Headers and Cookies are automatically sent
		 */
		$response->sendHeaders();
		$response->sendCookies();

		/**
		 * Return the response
		 */
		return $response;
	}

}
