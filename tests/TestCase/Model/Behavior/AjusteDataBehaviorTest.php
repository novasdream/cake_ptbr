<?php
/**
 * Teste do Behavior AjusteData
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @author        Juan Basso <jrbasso@gmail.com>
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
namespace CakePtbr\Test\TestCase\Model\Behavior;


use Cake\ORM\Table;
use Cake\TestSuite\TestCase;


/**
 * CakePtbrNoticia
 *
 */
class NoticiaTable extends Table
{

    /**
     * Nome da model
     *
     * @var string
     * @access public
     */
    public $name = 'Noticia';

    /**
     * Usar tabela?
     *
     * @var boolean
     * @access public
     */
    public $useTable = false;

    public function exists($conditions)
    {
        return true;
    }


}

/**
 * CakePtbrNoticiaSemNada
 *
 */
class NoticiaSemNada extends NoticiaTable
{

    /**
     * Nome da model
     *
     * @var string
     * @access public
     */
    public $name = 'NoticiaSemNada';


    /**
     * Bootstrap
     * @param array $config
     */
    public function initialize($config)
    {
        $this->addBehavior("AjustDataBehavior");
    }

}

/**
 * CakePtbrNoticiaString
 *
 */
class NoticiaStringTable extends NoticiaTable
{

    /**
     * Nome da model
     *
     * @var string
     * @access public
     */
    public $name = 'NoticiaString';

    public function initialize($config)
    {
        $this->addBehavior("AjusteDataBehavior");
    }
}

/**
 * CakePtbrNoticiaArrayVazio
 *
 */
class NoticiaArrayVazio extends NoticiaTable
{

    /**
     * Nome da model
     *
     * @var string
     * @access public
     */
    public $name = 'CakePtbrNoticiaArrayVazio';

    /**
     * Lista de Behaviors
     *
     * @var array
     * @access public
     */
    public $actsAs = array('CakePtbr.AjusteData' => array());

}

/**
 * CakePtbrNoticiaArrayComCampo
 *
 */
class NoticiaArrayComCampo extends NoticiaTable
{

    /**
     * Nome da model
     *
     * @var string
     * @access public
     */
    public $name = 'CakePtbrNoticiaArrayComCampo';

    /**
     * Lista de Behaviors
     *
     * @var array
     * @access public
     */
    public $actsAs = array('CakePtbr.AjusteData' => array('data'));

}

/**
 * CakePtbrNoticiaArrayComCampos
 *
 */
class NoticiaArrayComCamposTable extends NoticiaTable
{

    /**
     * Nome da model
     *
     * @var string
     * @access public
     */
    public $name = 'CakePtbrNoticiaArrayComCampos';

    /**
     * Lista de Behaviors
     *
     * @var array
     * @access public
     */
    public $actsAs = array('CakePtbr.AjusteData' => array('data', 'publicado'));
}

/**
 * AjusteData Test Case
 *
 */
class AjusteDataBehaviorTest extends TestCase
{
    /**
     * Envio
     *
     * @var array
     * @access protected
     */
    public $_envio = array(
        'id' => 1,
        'nome' => 'Teste',
        'data' => '20/03/2009',
        'data_falsa' => '30/01/2009',
        'publicado' => '01/01/2010'
    );

    /**
     * testSemNada
     *
     * @retun void
     * @access public
     */
    public function testSemNada()
    {
        $esperado = array(
            'CakePtbrNoticiaSemNada' => array(
                'id' => 1,
                'nome' => 'Teste',
                'data' => '20/03/2009',
                'data_falsa' => '30/01/2009',
                'publicado' => '01/01/2010'
            )
        );
        $this->_testModel('CakePtbrNoticiaSemNada', $esperado);
    }

    /**
     * Método auxiliar para executar os testes
     *
     * @param string $nomeModel Nome da model
     * @param array $esperado Valor esperado
     * @retun void
     * @access protected
     */
    public function _testModel($nomeModel, $esperado)
    {
        $Model = new $nomeModel();
        $Model->create();
        $Model->save(array($nomeModel => $this->_envio));
        $this->assertEqual($Model->data, $esperado);
    }

    /**
     * testString
     *
     * @retun void
     * @access public
     */
    public function testString()
    {
        $esperado = array(
            'CakePtbrNoticiaString' => array(
                'id' => 1,
                'nome' => 'Teste',
                'data' => '2009-03-20',
                'data_falsa' => '30/01/2009',
                'publicado' => '01/01/2010'
            )
        );
        $this->_testModel('CakePtbrNoticiaString', $esperado);
    }

    /**
     * testArrayVazio
     *
     * @retun void
     * @access public
     */
    public function testArrayVazio()
    {
        $esperado = array(
            'CakePtbrNoticiaArrayVazio' => array(
                'id' => 1,
                'nome' => 'Teste',
                'data' => '20/03/2009',
                'data_falsa' => '30/01/2009',
                'publicado' => '01/01/2010'
            )
        );
        $this->_testModel('CakePtbrNoticiaArrayVazio', $esperado);
    }

    /**
     * testArrayComCampo
     *
     * @retun void
     * @access public
     */
    public function testArrayComCampo()
    {
        $esperado = array(
            'CakePtbrNoticiaArrayComCampo' => array(
                'id' => 1,
                'nome' => 'Teste',
                'data' => '2009-03-20',
                'data_falsa' => '30/01/2009',
                'publicado' => '01/01/2010'
            )
        );
        $this->_testModel('CakePtbrNoticiaArrayComCampo', $esperado);
    }

    /**
     * testArrayComCampos
     *
     * @retun void
     * @access public
     */
    public function testArrayComCampos()
    {
        $esperado = array(
            'CakePtbrNoticiaArrayComCampos' => array(
                'id' => 1,
                'nome' => 'Teste',
                'data' => '2009-03-20',
                'data_falsa' => '30/01/2009',
                'publicado' => '2010-01-01'
            )
        );
        $this->_testModel('CakePtbrNoticiaArrayComCampos', $esperado);
    }

}
