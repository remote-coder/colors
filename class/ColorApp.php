<?php
class ColorApp
{
	/**
	 * 
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
	 * - for 'votes', if a 'color' filter is specified, filter on the color and
	 *   return those rows, if not then return it all
	 */
	protected function _gotGetSomeData($request=array()) {
		$data = array();
		$qualifiers = array();
		
		//Has to be a type we approve of, otherwise you're getting a blank json return
		if(array_key_exists('type',$request)) {
			$qualifiers['type']=$request['type'];
			switch ($request['type']) {
				case 'colors':
					$pseudoTable = array(
						array('color_id'=>10, 'color'=>'Red'),
						array('color_id'=>20, 'color'=>'Orange'),
						array('color_id'=>30, 'color'=>'Yellow'),
						array('color_id'=>40, 'color'=>'Green'),
						array('color_id'=>50, 'color'=>'Blue'),
						array('color_id'=>60, 'color'=>'Indigo'),
						array('color_id'=>75, 'color'=>'Violet'),
					);
					
					$data = $pseudoTable;
				break;
				
				case 'votes':
					$pseudoTable = array(
						array('city'=>'Anchorage', 'color'=>'Blue', 'votes'=>10000,),
						array('city'=>'Anchorage', 'color'=>'Yellow', 'votes'=>15000,),
						array('city'=>'Brooklyn', 'color'=>'Red', 'votes'=>100000),
						array('city'=>'Brooklyn', 'color'=>'Blue', 'votes'=>250000),
						array('city'=>'Detroit', 'color'=>'Red', 'votes'=>160000),
						array('city'=>'Selma', 'color'=>'Yellow', 'votes'=>15000),
						array('city'=>'Selma', 'color'=>'Violet', 'votes'=>5000),
					);
					
					if(array_key_exists('color', $request)) {
						$qualifiers['color']=$request['color'];
						foreach ($pseudoTable as $row) {
							if(array_key_exists('color', $row) && $request['color']== $row['color']) {
								$data[]=$row;
							}
						}
					}
					
				break;
				default:
					;
				break;
			}
		}
		
		return array('qualified'=> $qualifiers, 'data'=>$data );
	}
}