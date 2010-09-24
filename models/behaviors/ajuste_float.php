<?php
/**
 * Behavior para ajustar os campos float
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @author        Juan Basso <jrbasso@gmail.com>
 * @author        Daniel Pakuschewski <contato@danielpk.com.br>
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */

/**
 * AjusteFloatBehavior
 *
 * @link http://wiki.github.com/jrbasso/cake_ptbr/behavior-ajustefloat
 */
class AjusteFloatBehavior extends ModelBehavior {

/**
 * Campos do tipo float
 *
 * @var array
 * @access public
 */
	var $floatFields = array();

/**
 * Setup
 *
 * @param object $model
 * @param array $config
 * @return void
 * @access public
 */
	function setup(&$model, $config = array()) {
		$this->floatFields[$model->alias] = array();
		foreach ($model->_schema as $field => $spec) {
			if ($spec['type'] == 'float') {
				$this->floatFields[$model->alias][] = $field;
			}
		}
	}
	
/**
 * Before Find
 * Transforma o valor de BRL para o formato SQL antes de executar uma query
 * com conditions.
 * 
 * @param object $model
 * @return array
 * @access public
 */
	function beforeFind(&$model, $query) {
		if (is_array($query['conditions']) && count($query['conditions']) > 0) {
			foreach ($query['conditions'] as $field => $value) {
				if (strpos($field, '.') == false) {
					$field = $model->alias . '.' . $field;
				}
				list($Model, $field) = explode('.', $field);
				$Model = ($Model != $model->alias) ? $model->{$Model} : $model;
				if ($Model->hasField($field) && $Model->_schema[$field]['type'] == 'float') {
					$value = str_replace(',', '.', $value);
					if (isset($query['conditions'][$field])) {
						$query['conditions'][$field] = $value;
					}
					if (isset($query['conditions'][$Model->alias . '.' . $field])) {
						$query['conditions'][$Model->alias . '.' . $field] = $value;
					}
				}
			}
		}
		return($query);
	}

/**
 * Before Save
 *
 * @param object $model
 * @return void
 * @access public
 */
	function beforeSave(&$model) {
		$data =& $model->data[$model->alias];
		foreach ($data as $name => $value) {
			if (in_array($name, $this->floatFields[$model->alias])) {
				$data[$name] = str_replace(array('.', ','), array('', '.'), $value);
			}
		}

		return true;
	}

/**
 * After Find
 *
 * @param object $model
 * @param array $results
 * @param boolean $primary
 * @return void
 * @access public
 * @deprecated Isto deve ser feito na view
 */
	function afterFind(&$model, $results, $primary) {
		foreach ($results as $key => $r) {
			if (isset($r[$model->alias]) && is_array($r[$model->alias])) {
				foreach (array_keys($r[$model->alias]) as $arrayKey) {
					if (in_array($arrayKey, $this->floatFields[$model->alias])) {
						$results[$key][$model->alias][$arrayKey] = number_format($r[$model->alias][$arrayKey], 2, ',', '.');
					}
				}
			}
		}
		return $results;
	}
}
