<?php
/*
Name: PHP Box
Author: Tim Milligan
Version: 2.1.1
Description: An extension of the Thesis Text Box that supports PHP code.
Class: vzm_php_box

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/

class vzm_php_box extends thesis_box {
	protected function translate() {
		$this->title = $this->name = __('PHP Box', $this->_class);
	}

	protected function construct() {
		global $thesis;
		$filters = !empty($this->options['filter']['on']) ?
			array(
				'wptexturize' => false,
				'convert_smilies' => false,
				'convert_chars' => false,
				'wpautop' => false,
				'shortcode_unautop' => false,
				'do_shortcode' => false) :
			array(
				'wptexturize' => false,
				'convert_smilies' => false,
				'convert_chars' => false,
				'do_shortcode' => false);
		$thesis->wp->filter($this->_id, $filters);
		
		global $vzm_ah;
		if(is_admin()) {
			if(!isset($vzm_ah)) { // Check if the Asset Handler has already been created once, no point in creating the same asset handler multiple times.
				if(!class_exists('vzm_asset_handler')) // Load the asset handler class if it hasn't been already.
					require_once( dirname(__FILE__) . '/vzm_asset_handler.php');
					
				$vzm_ah = new vzm_asset_handler;
			}
		}
	}

	protected function html_options() {
		global $thesis;
		$html = $thesis->api->html_options(array(
			'div' => 'div',
			'none' => sprintf(__('No %s wrapper', $this->_class), $thesis->api->base['html'])), 'div');
		$html['html']['dependents'] = array('div');
		$html['id']['parent'] = $html['class']['parent'] = array('html' => 'div');
		return $html;
	}

	protected function options() {
		global $thesis;
		return array(
			'text' => array(
				'type' => 'textarea',
				'rows' => 8,
				'code' => true,
				'label' => sprintf(__('%s/%s', $this->_class), $thesis->api->base['php'], $thesis->api->base['html']),
				'tooltip' => sprintf(__('This box allows you to insert plain text and/or %2$s/%1$s. All text will be formatted just like a normal WordPress post, and all valid %1$s tags are allowed.', $this->_class), $thesis->api->base['html'], $thesis->api->base['php'])),
			'filter' => array(
				'type' => 'checkbox',
				'options' => array(
					'on' => __('enable automatic <code>&lt;p&gt;</code> tags for this PHP Box', $this->_class))));
	}

	public function html($args = array()) {
		global $thesis;
		extract($args = is_array($args) ? $args : array());
		$tab = str_repeat("\t", !empty($depth) ? $depth : 0);
		$html = !empty($this->options['html']) ? ($this->options['html'] == 'none' ? false : $this->options['html']) : 'div';
		if (empty($this->options['text']) && !is_user_logged_in()) return;
		echo
			($html ?
			"$tab<div" . (!empty($this->options['id']) ? ' id="' . trim($thesis->api->esc($this->options['id'])) . '"' : '') . ' class="' . (!empty($this->options['class']) ? trim($thesis->api->esc($this->options['class'])) : 'text_box') . "\">\n" : ''),
			$tab, ($html ? "\t" : ''), trim(apply_filters($this->_id, !empty($this->options['text']) ?
				$this->eval_php(stripslashes($this->options['text'])) :
				sprintf(__('This is a Text Box named %1$s. You can write anything you want in here, and Thesis will format it just like a WordPress post. <a href="%2$s">Click here to edit this Text Box</a>.', $this->_class), $this->name, admin_url("admin.php?page=thesis&canvas=$this->_id")))), "\n",
			($html ?
			"$tab</div>\n" : '');
	}
	
	protected function eval_php($content) {
		ob_start();
			eval("?>$content<?php ");
		return ob_get_clean();
	}
}