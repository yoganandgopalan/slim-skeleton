<?php
namespace App\Service;
use Slim\Exception\NotFoundException;
use Exception;
use App\Lib\SortFilter\SortFilter;


/**
* @property SortFilter     SortFilter
*/
abstract class BaseService
{
	protected  $_id = 'id';


	/**
	* @param string[] $params
	*
	* @return array
	*/
	public function create($params)
	{
		try
		{
			$data = $this->_model->create($params);
			$return[$this->_id] = $data->id ;
			return $return;
		}
		catch(Exception $e)
		{
			throw new Exception($e->getMessage());
		}
	}

	/**
	* @param string[] $params
	*
	* @return array
	*/
	public function get($params)
	{
		try
		{
			$sortFilter = new SortFilter();
			$data =  $sortFilter->parseSingle($this->_request,$this->_model,$params[$this->_id])->cleanup(true)->getResponse();
			//$data = $this->_model->whereId($params[$this->_id])->first();
		}
		catch(Exception $e)
		{
			throw new Exception($e->getMessage());
		}
		if ($data)
		{
			return $data;
		}
		else
		{
			throw new NotFoundException($this->_request, $this->_response);
		}

	}

	/**
	* @return array
	*/
	public function gets()
	{
		try
		{
			$sortFilter = new SortFilter();
			return $sortFilter->parseMultiple($this->_request,$this->_model)->cleanup(true)->getResponse();
			//return $this->_model->all()->toArray();
		}
		catch(Exception $e)
		{
			throw new Exception($e->getMessage());
		}
	}

	/**
	* @param string[] $params
	*
	* @return array
	*/
	public function put($params)
	{
		try
		{
			$collection = $this->_model->whereId($params[$this->_id]);
			$data = $collection->get();
		}
		catch(Exception $e)
		{
			throw new Exception($e->getMessage());
		}

		if (!$data->isEmpty())
		{
			$collection->update($params);
			return $params;
		}
		else
		{
			throw new NotFoundException($this->_request, $this->_response);
		}
	}

	/**
	* @param string[] $params
	*
	* @return array
	*/
	public function delete($params)
	{

		try
		{
			$collection = $this->_model->whereId($params[$this->_id]);
			$data = $collection->get();
		}
		catch(Exception $e)
		{
			throw new Exception($e->getMessage());
		}

		if (!$data->isEmpty())
		{
			return $collection->delete();
		}
		else
		{
			throw new NotFoundException($this->_request, $this->_response);
		}
	}
}
