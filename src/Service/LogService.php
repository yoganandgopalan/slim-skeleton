<?php
namespace App\Service;

use App\Service\BaseService;
use App\Model\Logs;
//use Respect\Validation\Validator as V;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * @property BaseService     BaseService
 * @property Validator  v
 * @property Logs  Logs
 */

class LogService extends BaseService
{

	public  $_request;
	public  $_response;
	public  $_model;

	/**
	* Constructor.
	*
	* @param Request $request
	* @param Response $response
	*/
	public function __construct(Request $request, Response $response )
	{
		$this->_model = new Logs ();
		$this->_request = $request;
		$this->_response = $response;
		//$this->_id = 'id';
	}

	// /**
	// * @param string[] $params
	// *
	// * @return array
	// */
	// public function create($params)
	// {
	// 	return parent::create($params);
	// }
	// /**
	// * @param string[] $params
	// *
	// * @return array
	// */
	// public function get($params)
	// {
	// 	return parent::get($params);
	// }
	// /**
	// * @param string[] $params
	// *
	// * @return array
	// */
	// public function gets()
	// {
	// 	return parent::gets();
	// }
	// /**
	// *
	// * @return array
	// */
	// public function put($params)
	// {
	// 	return parent::put($params);
	// }
	// /**
	// * @param string[] $params
	// *
	// * @return array
	// */
	// public function delete($params)
	// {
	// 	return parent::delete($params);
	// }

}
