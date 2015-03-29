<?php
/**
 * Behavior para ajustar o formato de data
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @author        Juan Basso <jrbasso@gmail.com>
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
namespace CakePtbr\Model\Behavior;

use Cake\Event\Event;
use Cake\ORM\Behavior;
use Cake\ORM\Entity;
use Cake\ORM\Table;


/**
 * AjusteDataBehavior
 *
 * @link http://wiki.github.com/jrbasso/cake_ptbr/behavior-ajustedata
 */
class AjusteDataBehavior extends Behavior
{

    /**
     * Configuração dos campos
     *
     * @var array
     * @access public
     */
    public $campos;

    public function initialize(array $config = [])
    {
        if (empty($config)) {
            // Caso não seja informado os campos, ele irá buscar no schema
            $this->campos[$this->_table->alias()] = $this->_buscaCamposDate();
        } elseif (!is_array($config)) {
            $this->campos[$this->_table->alias()] = [$config];
        } else {
            $this->campos[$this->_table->alias()] = $config;
        }
    }

    /**
     * @param Event $event
     * @param Entity $entity
     * @param \ArrayObject $options
     * @return bool
     */
    public function beforeSave(Event $event, Entity $entity, \ArrayObject $options = [] )
    {
        return $this->ajustarDatas($entity);
    }

    /**
     * Corrigir as datas
     * @param Entity $entity Uma instância
     * @return bool
     */
    public function ajustarDatas(Entity $entity)
    {
        $data =& $entity->toArray();
        foreach ($this->campos[$this->_table->alias()] as $campo) {
            if (isset($data[$campo])) {
                // DATA E HORA
                if (preg_match('/\d{1,2}\/\d{1,2}\/\d{2,4} \d{1,2}\:\d{1,2}/', $data[$campo])) {
                    $novaData = explode(' ', $data[$campo]);
                    list($dia, $mes, $ano) = explode('/', $novaData[0]);
                    list($hora, $minuto, $segundo) = explode(':', $novaData[1]);
                    if (strlen($ano) == 2) {
                        if ($ano > 50) $ano += 1900;
                        else $ano += 2000;
                    }
                    $entity->set($campo, "$ano-$mes-$dia $hora:$minuto:$segundo");
                } // DATA
                elseif (preg_match('/\d{1,2}\/\d{1,2}\/\d{2,4}/', $data[$campo])) {
                    list($dia, $mes, $ano) = explode('/', $data[$campo]);
                    if (strlen($ano) == 2) {
                        if ($ano > 50) $ano += 1900;
                        else $ano += 2000;
                    }
                    $entity->set("$ano-$mes-$dia");
                }
            }
        }
        return true;
    }

    /**
     * Buscar campos de data nos dados da model
     *
     * @return array Lista dos campos
     * @access protected
     */
    public function _buscaCamposDate()
    {
        $colunas = $this->_table->schema()->columns();
        if (!is_array($colunas)) {
            return [];
        }
        $saida = [];
        foreach ($colunas as $campo) {
            if ($this->_table->schema()->columnType($campo) === 'date' && !in_array($campo, array('created', 'updated', 'modified'))) {
                $saida[] = $campo;
            }
        }
        return $saida;
    }
}
