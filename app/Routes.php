<?php
/**
 * Routes - all standard routes are defined here.
 *
 * @author David Carr - dave@daveismyname.com
 * @version 3.0
 */

/** Create alias for Router. */
use Core\Router;
use Helpers\Hooks;

/** Get the Router instance. */
$router = Router::getInstance();

/** Define static routes. */

// Default Routing
Router::any('', 'App\Controllers\DocumentController@index');
Router::any('docs', 'App\Controllers\DocumentController@index');
Router::any('docs/add', 'App\Controllers\DocumentController@add');
Router::any('docs/save', 'App\Controllers\DocumentController@save');
Router::any('docs/remove/(:num)', 'App\Controllers\DocumentController@remove');
Router::any('docs/view/(:num)', 'App\Controllers\DocumentController@view');
Router::any('docs/edit/(:num)', 'App\Controllers\DocumentController@edit');

Router::get('docs/find', 'App\Controllers\DocumentController@searchResult');
Router::post('docs/find', 'App\Controllers\DocumentController@formSearch');

Router::any('admin', 'App\Controllers\AdministrationController@configuration');
Router::any('admin/attributes', 'App\Controllers\AdministrationController@showAttributeIndex');
Router::post('admin/attributes/save', 'App\Controllers\AdministrationController@saveAttribute');
/** End default routes */

/** Module routes. */
$hooks = Hooks::get();
$hooks->run('routes');
/** End Module routes. */

/** If no route found. */
Router::error('Core\Error@index');

/** Execute matched routes. */
$router->dispatch();
