<?php
namespace App\Controllers;

use App\Core\Controller\Controller;
use Respect\Validation\Validator as V;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Service\LogService;


/**
 * @property Controller     Controller
 * @property Validator  v
 */
class LogController extends Controller
{
	/**
	* @var _primaryKey is the primary key for the table
	* @var _fields_create mandatory fields to create a row in the table
	*/
	protected  $_primaryKey = 'id';
	protected  $_fields_create  = array('message');


	/**
	* Returns a "201 Created" response with a location header.
	*
	* @param Request  $request
	* @param Response $response
	* @param string   $route
	*
	* @return Response
	*/
	public function postLog(Request $request, Response $response)
	{
		$rules = array(
		    'message' => array('rules' => V::notBlank(),'messages' => array('notBlank' => 'Field cannot be blank')),
		);

		$params = $this->params($request, $this->_fields_create);

		if ($this->validator->validate($params, $rules)->isValid())
		{

			$logService = new LogService($request,$response);
			$return = $logService->create($params);
			return $this->created($response, 'post_log',$return);
		}
		else
		{
			return $this->validationErrors($response);
		}

	}

	/**
	* Returns a "200 Ok" response with JSON data.
	*
	* @param Request  $request
	* @param Response $response
	* @param int    $id
	*
	* @return Response
	*/
	public function getLog(Request $request, Response $response, $id)
	{
		$params = array($this->_primaryKey=>$id);
		$rules = array(
		    $this->_primaryKey => array('rules' => V::notBlank()->intVal(),'messages' => array('notBlank' => 'Field cannot be blank','intVal' => 'Field is not int')),
		);

		if ($this->validator->validate($params, $rules)->isValid())
		{
			$logService = new LogService($request,$response);
			$return = $logService->get($params);
			return $this->json($response,$return, 200);	
		}
		else
		{
			return $this->validationErrors($response);
		}
	}

	/**
	* Returns a "200 Ok" response with JSON data.
	*
	* @param Request  $request
	* @param Response $response
	*
	* @return Response
	*/
	public function getLogs(Request $request, Response $response)
	{
		$logService = new LogService($request,$response);
		$return = $logService->gets();
		return $this->json($response,$return, 200);
	}

	/**
	* Returns a "200 Ok" response with JSON data.
	*
	* @param Request  $request
	* @param Response $response
	* @param int    $id
	*
	* @return Response
	*/
	public function putLog(Request $request, Response $response, $id)
	{
		$rules = array(
		    $this->_primaryKey => array('rules' => V::notBlank()->intVal(),'messages' => array('notBlank' => 'Field cannot be blank','intVal' => 'Field is not int')),
		    'message' => array('rules' => V::notBlank(),'messages' => array('notBlank' => 'Field cannot be blank')),
		);

		$fields = array('message');

		$params = $this->params($request, $fields);
		$params[$this->_primaryKey] = $id;

		if ($this->validator->validate($params, $rules)->isValid())
		{
			$logService = new LogService($request,$response);
			$return = $logService->put($params);
			return $this->json($response,$return, 200);
		}
		else
		{
			return $this->validationErrors($response);
		}
	}

	/**
	* Returns a "204 Ok" response with JSON data.
	*
	* @param Request  $request
	* @param Response $response
	* @param int    $id
	*
	* @return Response
	*/
	public function deleteLog(Request $request, Response $response, $id)
	{
		$params = array($this->_primaryKey=>$id);
		$rules = array(
		    $this->_primaryKey => array('rules' => V::notBlank()->intVal(),'messages' => array('notBlank' => 'Field cannot be blank','intVal' => 'Field is not int')),
		);

		if ($this->validator->validate($params, $rules)->isValid())
		{
			$logService = new LogService($request,$response);
			$return = $logService->delete($params);
			return $this->json($response,$return, 204);
		}
		else
		{
			return $this->validationErrors($response);
		}
	}
}
