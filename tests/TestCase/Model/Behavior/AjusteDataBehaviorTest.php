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

use Cake\Datasource\ConnectionManager;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * AjusteData Test Case
 *
 */
class AjusteDataBehaviorTest extends TestCase
{

    public $fixtures = [
        "plugin.CakePtbr.noticias"
    ];

    /**
     * @var Table $Noticias
     */
    public $Noticias;
    private $db;


    /**
     * setup method
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->Noticias = TableRegistry::get("CakePtbr.Noticias");
        $this->db = ConnectionManager::get('test');
    }

    /**
     * tear down
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();
        $this->Noticias->removeBehavior("CakePtbr.AjusteData");
        $this->Noticias->removeBehavior("AjusteData");
//        unset($this->Noticias, $this->Noticias);
    }

    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();
        TableRegistry::clear();
    }


    public function testDeteccaoAutomatica()
    {
        $this->Noticias->addBehavior("CakePtbr.AjusteData");
        $noticia = $this->__preparaNoticia();

        $this->assertEquals("2015-03-22", $noticia->get("autorizado_em"));
        $this->assertEquals("2015-03-25 16:42:05", $noticia->get("publicado_em"));

        $this->Noticias->removeBehavior("CakePtbr.AjusteData");
    }


    /**
     * testCampoEmArray
     *
     * @retun void
     * @access public
     */
    public function testCampoEmArray()
    {
        $this->Noticias->addBehavior("CakePtbr.AjusteData", ["autorizado_em"]);
        $noticia = $this->__preparaNoticia();

        $this->assertEquals("2015-03-22", $noticia->get("autorizado_em"));

        $this->Noticias->removeBehavior("CakePtbr.AjusteData");
    }

    /**
     * testCamposEmArray
     *
     * @retun void
     * @access public
     */
    public function testCamposEmArray()
    {
        $this->Noticias->addBehavior("CakePtbr.AjusteData", ["autorizado_em", "publicado_em"]);
        $noticia = $this->__preparaNoticia();

        $this->assertEquals("2015-03-22", $noticia->get("autorizado_em"));
        $this->assertEquals("2015-03-25 16:42:05", $noticia->get("publicado_em"));

        $this->Noticias->removeBehavior("CakePtbr.AjusteData");
    }

    /**
     * @return \Cake\Datasource\EntityInterface|mixed
     */
    private function __preparaNoticia()
    {
        $noticia = $this->Noticias->get(1);
        $noticia->set("id", null);
        $noticia->isNew(true);
        $noticia->set("autorizado_em", "22/03/2015");
        $noticia->set("publicado_em", "25/03/2015 16:42:05");
        $this->Noticias->save($noticia);
        return $noticia;
    }


}
