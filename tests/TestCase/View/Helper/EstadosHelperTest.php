<?php
/**
 * Testes do helper de estados
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @author        Juan Basso <jrbasso@gmail.com>
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
namespace CakePtbr\Test\TestCase\View\Helper;

use Cake\Controller\Controller;
use Cake\Network\Request;
use Cake\Network\Response;
use Cake\TestSuite\TestCase;
use Cake\View\Helper\FormHelper;
use Cake\View\Helper\HtmlHelper;
use Cake\View\View;
use CakePtbr\Lib\Estados;
use CakePtbr\View\Helper\EstadosHelper;


/**
 * Controller Test
 *
 */
class ControllerTestController extends Controller
{

    /**
     * Nome do controller
     *
     * @var string
     * @access public
     */
    public $name = 'ControllerTest';

    /**
     * Uses
     *
     * @var array
     * @access public
     */
    public $uses = null;
}

/**
 * Estado Test Case
 *
 */
class EstadosHelperTest extends TestCase
{

    /**
     * Estados
     *
     * @var object
     * @access public
     */
    public $Estados = null;

    /**
     * Lista dos estados
     *
     * @var string
     * @access public
     */
    public $listaEstados;

    /**
     * setUp
     *
     * @retun void
     * @access public
     */
    public function setUp()
    {
        parent::setUp();
        $Controller = new ControllerTestController(new Request(), new Response());
        $View = new View($Controller);
        $this->Estados = new EstadosHelper($View);
        $this->Estados->Form = new FormHelper($View);
        $this->Estados->Form->Html = new HtmlHelper($View);

        $this->listaEstados = Estados::lista();
    }

    /**
     * testSelect
     *
     * @retun void
     * @access public
     */
    public function testSelect()
    {
        $expected = array('select' => array('name' => 'data[Model][uf]', 'id' => 'ModelUf'));
        foreach ($this->listaEstados as $sigla => $nome) {
            $expected[] = array('option' => array('value' => $sigla));
            $expected[] = $nome;
            $expected[] = '/option';
        }
        $expected[] = '/select';
        $result = $this->Estados->select('Model.uf');
        $this->assertTags($result, $expected);

        $expected = array('select' => array('name' => 'data[Model][uf]', 'id' => 'ModelUf'));
        foreach ($this->listaEstados as $sigla => $nome) {
            $expected[] = array('option' => array('value' => $sigla));
            $expected[] = $sigla;
            $expected[] = '/option';
        }
        $expected[] = '/select';
        $result = $this->Estados->select('Model.uf', null, array('uf' => true));
        $this->assertTags($result, $expected);
    }

}
