<?php
class ColorApp
{
	/**
	 * 
	 * @var PDO
	 */
	private $_db = null;
	
	/**
	 * Set up the mini app
	 * @param string $configFileName
	 * @param string $section
	 * @throws Exception
	 */
	public function __construct($configFileName, $section = 'test') {
		//set an error handler and exceptopn handler
		set_error_handler('error_handler');
		set_exception_handler('exception_handler');
		
		//make sure that config file exists, otherwise you're going nowhere fast
		if( !file_exists("config/{$configFileName}.ini") ) {
			throw new Exception("Invalid Config File");
		}	
		
		//now parse the ini
		$config = parse_ini_file("config/{$configFileName}.ini", true);
		//if the section you speficied doesn't exist, you got problems dude
		if(!isset($config[$section])) {
			throw new Exception("Invalid Config Environment");
		}
		
		//store the successful PDO so you can DI it in anything data related that needs it
		$this->_db = DataModel::initDBConnection($config[$section]);
		
	}
	

	
	/**
	 * This is the front-end controller / router.  It is the traffic cop for your request.
	 */
	public function start() {
		
		//burst the REQUEST_URI so I can pull out the local path items 
		//and then figure out is an operation was requested
		// because I don't like get parms for navigation
		$request = explode('/', $_SERVER['REQUEST_URI'] );
		
		$operation=null;
		if(count($request)>1 ){
			array_shift($request);
			if(reset($request)=='stash'){
				array_shift($request);
				$operation = array_shift($request);
			}
		}

		//now, if I have an operation, do it.  Otherwise back to the default view
		switch ($operation) {
			case 'GETSOME':
				//get some data and
				$request = $_REQUEST; 
				$data = $this->_gotGetSomeData($request);
				echo json_encode($data);
			break;
			
			default:
				$this->_renderIndexPage();
			break;
		}

	}
	
	/**
	 * Render the index page!
	 */
	protected function _renderIndexPage() {
		$vo = new ViewObject();
		//link in the js
		$vo->appendJs('<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>');
		$vo->appendJs('<script src="/stash/js/ColorApp.js"></script>');

		//link in the css
		$csss = glob('css/*');
		foreach ($csss as $css) {
			$vo->appendCss(file_get_contents($css));
		}
		//link in the templates
		$templates = glob('template/js/*');
		foreach ($templates as $template) {
			$vo->appendTemplate(file_get_contents($template));
		}
		
		//then execute it via the include
		include 'template/index.phtml';
	}
	

	/**
	 * Get some data here
	 * @param array $request
	 *
	 *	This is an example, we're not launching a rocket here 
	 *	otherwise we'd do it based on abstracted data models with adapters for 
	 *	the various data sources (ini, db's of all kinds, xml, soap, rest) and
	 *	pagination		
	 * 	
	 * - for 'colors', just pour the whole content down
	 * - for 'votes' - filtered by color
	 */
	protected function _gotGetSomeData($request=array()) {
		$data = array();
		$qualifiers = array();
		//Has to be a type we approve of, otherwise you're getting a blank json return
		if(array_key_exists('type',$request)) {
			$qualifiers['type']=$request['type'];
			switch ($request['type']) {
				case 'colors':
					$data = DataModel::getColorData($this->_db);
				break;
				case 'votes':
					$qualifiers['color']=$request['color'];  
					$data = DataModel::getVoteDataByColor($this->_db, array('color'=>$request['color']));
				break;
				default:
					;
				break;
			}
		}
		
		return array('qualified'=> $qualifiers, 'data'=>$data );
	}
}