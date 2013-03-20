<?php
/*
Name: PHP Box
Author: Tao Schencks, Modified by Tim Milligan
Version: 1.2
Description: A Thesis 2 Box That Accepts PHP code
Class: thesis_php_box
*/


class thesis_php_box extends thesis_box {
	
	protected function translate() {
		$this->title = $this->name = __('PHP Box', 'tpb');
	}
	
	protected function options() {
		global $thesis;
		return array(
			'code' => array(
				'type' => 'textarea',
				'rows' => 8,
				'code' => true,
				'label' => __('PHP code', 'tpb'),
				'tooltip' => sprintf(__('Enter your PHP code here, including the opening and closing tags', 'tpb')),
				'default' => ''
				),
			'id' => array(
				'type' => 'text',
				'width' => 'medium',
				'code' => true,
				'label' => $thesis->api->strings['html_id'],
				'tooltip' => $thesis->api->strings['id_tooltip']),
			'class' => array(
				'type' => 'text',
				'width' => 'medium',
				'code' => true,
				'label' => $thesis->api->strings['html_class'],
				'tooltip' => $thesis->api->strings['class_tooltip'])
			);
	}

	public function html($args = false) {
		global $thesis;
		extract($args = is_array($args) ? $args : array());
		$tab = str_repeat("\t", !empty($depth) ? $depth : 0);
		
		if(empty($this->options['code'])) return;
		
		$code = $this->eval_php(stripslashes($this->options['code']));		
		
		echo "$tab<div" . ($this->options['id'] ? ' id="' . trim($thesis->api->esc($this->options['id'])) . '"' : '') . ($this->options['class'] ? ' class="' . trim($thesis->api->esc($this->options['class'])) . '"' : '') . ">\n";
		echo "$tab\t".trim($code)."\n";
        echo "$tab</div>\n";
	}
	
	public function eval_php($content) {
		ob_start();
			eval("?>$content<?php ");
		return ob_get_clean();
	}
}

?>