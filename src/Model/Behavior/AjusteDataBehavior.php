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

    /**
     * Inicialização do behavior
     * @param array $config Array com a configuração básica do behavior
     * @return void
     */
    public function initialize(array $config = [])
    {
        if (empty($config)) {
            // Caso não seja informado os campos, ele irá buscar no schema
            $this->campos[$this->_table->alias()] = $this->__buscaCamposDate();
        } elseif (!is_array($config)) {
            $this->campos[$this->_table->alias()] = [$config];
        } else {
            $this->campos[$this->_table->alias()] = $config;
        }
    }

    /**
     * @param Event $event A instância de evento
     * @param Entity $entity A entidade a ser salva
     * @param \ArrayObject $options Opções do evento
     * @return bool
     */
    public function beforeSave(Event $event, Entity $entity, \ArrayObject $options = [])
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
                    $this->__ajustarDataHora($entity, $data, $campo);
                } elseif (preg_match('/\d{1,2}\/\d{1,2}\/\d{2,4}/', $data[$campo])) { // DATA
                    list($dia, $mes, $ano) = $this->__ajustarData($entity, $data, $campo);
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
    private function __buscaCamposDate()
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

    /**
     * Ajusta o valor do campo do tipo data de uma entidade.
     * @param Entity $entity Instância de entidade
     * @param string $campo Nome do campo a ser atualizado
     * @return array
     */
    private function __ajustarDataHora(Entity $entity, $campo)
    {
        $novaData = $this->__separarDataHora($entity->get($campo));
        list($dia, $mes, $ano) = explode('/', $novaData[0]);
        list($hora, $minuto, $segundo) = explode(':', $novaData[1]);
        if (strlen($ano) == 2) {
            if ($ano > 50) {
                $ano += 1900;
            } else {
                $ano += 2000;
            }
        }
        $entity->set($campo, "$ano-$mes-$dia $hora:$minuto:$segundo");
        return array($dia, $mes, $ano);
    }

    /**
     * Ajusta o valor do campo do tipo data de uma entidade.
     * @param Entity $entity Instância de entidade
     * @param string $campo Nome do campo a ser atualizado
     * @return array
     */
    private function __ajustarData(Entity $entity, $campo)
    {
        list($dia, $mes, $ano) = explode('/', $entity->get($campo));
        if (strlen($ano) == 2) {
            if ($ano > 50) {
                $ano += 1900;
                return array($dia, $mes, $ano);
            } else {
                $ano += 2000;
                return array($dia, $mes, $ano);
            }
        }
        $entity->set("$ano-$mes-$dia");
    }

    /**
     * Dada uma string que representa o timestamp separa a data e hora.
     * @param string $valor timestamp como string
     * @return array Array vazio, se receber como parametro uma string vazia ou um formato de data não válido.
     * Caso contrário, um array contendo a data e hora.
     */
    private function __separarDataHora($valor = "")
    {
        if (!isset($valor) || empty($valor)) {
            return [];
        }
        if (strpos($valor, "T")) {
            return explode("T", $valor);
        } elseif (strpos($valor, "t")) {
            return explode("t", $valor);
        } elseif (strpos($valor, " ")) {
            return explode(" ", $valor);
        }
        return [];
    }
}
