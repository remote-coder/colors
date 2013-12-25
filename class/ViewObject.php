<?php
/**
 * Well since we're doin a tiny app, we can do a tiny view object
 * @author Daniel
 *
 */
class ViewObject
{
	protected $js;
	protected $css;
	protected $body; 
	protected $templates;

	public function appendJs($value = '') {
		$this->js .= trim($value);
	}
	
	public function appendCss($value = '') {
		$this->css.= trim($value);
	}
	
	public function setBody($value = '') {
		$this->body = trim($value);
	}
	public function appendBody($value = '') {
		$this->body .= trim($value);
	}
	public function appendTemplate($value = '') {
		$this->templates.= trim($value);
	}
	public function getTemplates() {
		return $this->templates;
	}
	
	public function getJs() {
		return $this->js;
	}
	
	public function getCss() {
		return $this->css;
	}
	
	public function getBody() {
		return $this->body;
	}
	
}