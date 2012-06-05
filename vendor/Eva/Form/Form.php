<?php
namespace Eva\Form;

class Form extends \Zend\Form\Form
{
	protected static $instance;

	protected $baseElements;

	protected $mergeElements;

	protected $baseFilters;

	protected $mergeFilters;

	protected $defaultValues;

	protected $formMethod;

	protected $restfulMethod;

	protected $idPrefix;

	protected $hasIdPrefix = true;

	public function setDefaultValues($defaultValues)
	{
		$this->defaultValues = $defaultValues;
		return $this;
	}

	public function getDefaultValues()
	{
		return $this->defaultValues;
	}

	public function init(array $options = array())
	{
		$defaultOptions = array(
			'action' => '',
			'values' => array(),
			'method' => 'get',	
		);

		$options = array_merge($defaultOptions, $options);

		$method = $options['method'];
		if($method){
			$this->setMethod($method);
		}

		$action = $options['action'];
		if($action){
			$this->setAttribute('action', $action);
		}
		$values = $options['values'];

		$elements = $this->baseElements;
	
		if(is_object($values)){
			foreach($elements as $name => $element){
				if(isset($values->$name)){
					$element['attributes']['value'] = $values->$name;
				}
				$this->add($element);
			}
		} else {
			foreach($elements as $name => $element){
				$this->add($element);
			}
		}
		return $this;
	}

	public function attr()
	{
	}

	public function setMethod($method = '')
	{
		if(!$method){
			return $this;
		}

		$allowMethods = array('get', 'put', 'post', 'delete');
		$method = strtolower($method);

		if(false === in_array($method, $allowMethods)){
			throw new Exception\UnexpectedMethodException(sprintf(
                'Input Method %s is not correct http method',
                __METHOD__,
                $method
            ));
		}


		$restfulMethod = 'get';
		switch($method){
			case 'post' :
				$restfulMethod = 'post';
				break;
			case 'get' :
				break;
			case 'put' :
				$restfulMethod = 'put';
				$method = 'post';
				break;
			case 'delete' :
				$restfulMethod = 'delete';
				$method = 'post';
				break;
			default:
		}

		$this->setAttribute('method', $method);
		$this->restfulMethod = $restfulMethod;

		return $this;
	}

	public function restful()
	{
		return sprintf('<input type="hidden" name="_method" value="%s">', $this->restfulMethod);
	}


	public static function _()
	{
		return self::getInstance();
	}

	public static function getInstance()
	{
		if( is_null(self::$instance) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}
