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
namespace CakePtbr\Model\Behavior;

use Cake\Database\Expression\Comparison;
use Cake\Event\Event;
use Cake\ORM\Behavior;
use Cake\ORM\Entity;
use Cake\ORM\Query;


/**
 * AjusteFloatBehavior
 *
 * @link http://wiki.github.com/jrbasso/cake_ptbr/behavior-ajustefloat
 */
class AjusteFloatBehavior extends Behavior
{

    /**
     * Campos do tipo float
     *
     * @var array
     * @access public
     */
    public $floatFields = array();

    /**
     * Bootstraping the behavior
     *
     * @param array $config
     * @return void
     * @access public
     */
    public function initialize(array $config = [])
    {
        $this->floatFields[$this->_table->alias()] = array();
        foreach ($this->_table->schema()->columns() as $field) {
            if ($this->_table->schema()->columnType($field) == "float") {
                $this->floatFields[$this->_table->alias()][] = $field;
            }
        }
    }

    /**
     * Before save
     * Transforma o valor de BRL para o formato SQL antes de  salvar a entidade
     * no banco de dados
     *
     * @param Event $event
     * @param Entity $entity
     * @param \ArrayObject $config
     * @return bool
     * @access public
     */
    public function beforeSave(Event $event, Entity $entity, \ArrayObject $config = array())
    {
        foreach ($entity->toArray() as $field => $value) {
            if ($this->_table->hasField($field) && $this->_table->schema()->columnType($field) === "float") {
                if (!is_string($value) || preg_match('/^[0-9]+(\.[0-9]+)?$/', $value)) {
                    continue;
                }
                $entity->set($field, str_replace(array('.', ','), array('', '.'), $value));
            }
        }
        return true;
    }

    /**
     * Before Find
     * Transforma o valor de BRL para o formato SQL antes de executar uma query
     * com conditions.
     *
     * @param Event $event
     * @param Query $query
     * @param array $options
     * @param $primary
     * @return array
     * @access public
     */
    public function beforeFind(Event $event, Query $query, $options = [], $primary)
    {
        $query->clause("where")->traverse(function($comparison) use ($this) {
            /**
             * @var Comparison $comparison
             */
            if (isset($comparison)) {
                if ($this->_table->schema()->columnType($comparison->getField()) === "float") {
                    if (is_string($comparison->getValue()) && !preg_match('/^[0-9]+(\.[0-9]+)?$/', $comparison->getValue())) {
                        $comparison->setValue(str_replace(',', '.', str_replace('.', '', $comparison->getValue())));
                    }
                }
            }
        });
    }

}
