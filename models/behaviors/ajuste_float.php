<?php

class AjusteFloatBehavior extends ModelBehavior {

	var $campos;
  var $floatFields;

	function setup(&$model, $config = array()) {
    $this -> floatFields = array();

    foreach ($model -> _schema as $field => $spec) {
      if ($spec['type'] == 'float') {
        $this -> floatFields[] = $field;
      }
    }
	}

	function beforeSave(&$model) {
		$data =& $model->data[$model->name];
		foreach ($data as $name => &$value) {
      if (in_array($name, $this -> floatFields)) 
        $value = str_replace(array('.', ','), array('', '.'), $value);
      
		}

		return true;
	}

   function afterFind(&$model, $results, $primary) {
     foreach ($results as $key => &$r) {
      if (isset($r[$model->name]) && is_array($r[$model->name])) {
         foreach(array_keys($r[$model->name]) as $arrayKey) {
           if (in_array($arrayKey, $this -> floatFields)) {
            $r[$model->name][$arrayKey] = number_format($r[$model->name][$arrayKey], 2, ',', '.');
           }
         }
       }
     }
     return $results;
   }
}

?>