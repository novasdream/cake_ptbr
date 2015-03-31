<?php
/**
 * Testes das regras de pluralização e singularização
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @author        Juan Basso <jrbasso@gmail.com>
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
namespace CakePtbr\Test\TestCase\Config;

use Cake\Utility\Inflector;
use Cake\TestSuite\TestCase;
require_once ROOT . DS . 'config' . DS . 'inflections.php';


/**
 * Inflections Test Case
 *
 */
class CakePtbrInflectionsCase extends TestCase
{
    public function setUp()
    {
        parent::setUp();

    }


    /**
     * testPlural
     *
     * @retun void
     * @access public
     */
    public function testPlural()
    {
        $this->assertEquals('Compras', Inflector::pluralize('Compra'));
        $this->assertEquals('Caminhoes', Inflector::pluralize('Caminhao'));
        $this->assertEquals('Motores', Inflector::pluralize('Motor'));
        $this->assertEquals('Bordeis', Inflector::pluralize('Bordel'));
        $this->assertEquals('palavra_chaves', Inflector::pluralize('palavra_chave'));
        $this->assertEquals('Abris', Inflector::pluralize('Abril'));
        $this->assertEquals('Azuis', Inflector::pluralize('Azul'));
        $this->assertEquals('Alcoois', Inflector::pluralize('Alcool'));
        $this->assertEquals('Perfis', Inflector::pluralize('Perfil'));
        $this->testPluralIrregular();
    }

    /**
     * testSingular
     *
     * @retun void
     * @access public
     */
    public function testSingular()
    {
        $this->assertEquals(Inflector::singularize('Compras'), 'Compra');
        $this->assertEquals(Inflector::singularize('Caminhoes'), 'Caminhao');
        $this->assertEquals(Inflector::singularize('Motores'), 'Motor');
        $this->assertEquals(Inflector::singularize('Bordeis'), 'Bordel');
        $this->assertEquals(Inflector::singularize('palavras_chaves'), 'palavras_chave');
        $this->assertEquals(Inflector::singularize('Abris'), 'Abril');
        $this->assertEquals(Inflector::singularize('Azuis'), 'Azul');
        $this->assertEquals('Alcool', Inflector::singularize('Alcoois'));
        $this->testSingularIrregular();

    }

    /**
     * testNaoPluralizaveis
     *
     * @retun void
     * @access public
     */
    public function testNaoPluralizaveis()
    {
        // singularize
        $this->assertEquals(Inflector::singularize('Atlas'), 'Atlas');
        $this->assertEquals(Inflector::singularize('Lapis'), 'Lapis');
        $this->assertEquals(Inflector::singularize('Onibus'), 'Onibus');
        $this->assertEquals(Inflector::singularize('Pires'), 'Pires');
        $this->assertEquals(Inflector::singularize('Virus'), 'Virus');
        $this->assertEquals(Inflector::singularize('Torax'), 'Torax');
        // pluralize
        $this->assertEquals(Inflector::pluralize('Atlas'), 'Atlas');
        $this->assertEquals(Inflector::pluralize('Lapis'), 'Lapis');
        $this->assertEquals(Inflector::pluralize('Onibus'), 'Onibus');
        $this->assertEquals(Inflector::pluralize('Pires'), 'Pires');
        $this->assertEquals(Inflector::pluralize('Virus'), 'Virus');
        $this->assertEquals(Inflector::pluralize('Torax'), 'Torax');
    }

    /**
     * testSlug
     *
     * @retun void
     * @access public
     */
    public function testSlug()
    {
        $this->assertEquals(Inflector::slug('João'), 'Joao');
        $this->assertEquals(Inflector::slug('Conseqüência'), 'Consequencia');
        $this->assertEquals(Inflector::slug('Linguiça não útil água'), 'Linguica-nao-util-agua');
        $this->assertEquals(Inflector::slug('ÃÓ&'), 'AOe');
        $this->assertEquals(Inflector::slug('äü au Sandoval'), 'au-au-Sandoval');
    }

    /**
     * teste plural irregular
     * @return void
     */
    public function testPluralIrregular()
    {
// irregulares
        $this->assertEquals('Alemaes', Inflector::pluralize('Alemao'));
        $this->assertEquals('Maos', Inflector::pluralize('Mao'));
        $this->assertEquals('Caes', Inflector::pluralize('Cao'));
        $this->assertEquals('Repteis', Inflector::pluralize('Reptil'));
        $this->assertEquals('Sotaos', Inflector::pluralize('Sotao'));
        $this->assertEquals('Paises', Inflector::pluralize('Pais'));
        $this->assertEquals('Pais', Inflector::pluralize('Pai'));
    }

    public function testSingularIrregular()
    {
        // irregulares
        $this->assertEquals(Inflector::singularize('Perfis'), 'Perfil');
        $this->assertEquals(Inflector::singularize('Alemaes'), 'Alemao');
        $this->assertEquals(Inflector::singularize('Maos'), 'Mao');
        $this->assertEquals(Inflector::singularize('Caes'), 'Cao');
        $this->assertEquals(Inflector::singularize('Repteis'), 'Reptil');
        $this->assertEquals(Inflector::singularize('Sotaos'), 'Sotao');
        $this->assertEquals(Inflector::singularize('Paises'), 'Pais');
        $this->assertEquals(Inflector::singularize('Pais'), 'Pai');
    }

}
