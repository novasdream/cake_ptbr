<?php
/**
 * Testes das funções de internacionalização
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @author        Juan Basso <jrbasso@gmail.com>
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
namespace CakePtbr\Test\TestCase\Config;

use Cake\Core\Configure;
use Cake\I18n\I18n;
use Cake\TestSuite\TestCase;


include ROOT . DS . 'config' . DS . 'traducao_core.php';

/**
 * I18n Test Case
 *
 */
class I18nCase extends TestCase
{

    /**
     * testCore
     *
     * @retun void
     * @access public
     */
    public function testCore()
    {
        $this->assertEquals("pt_BR", I18n::locale());
        $this->assertEquals('Database não encontrado', __d('cake', ''));
        $this->assertEquals('uma semana atrás', __d('cake', 'about a week ago'));

    }

    /**
     * testTimeDefinition
     *
     * @retun void
     * @access public
     */
    public function testTimeDefinition()
    {
        $result = __('abday', 5);
        $expected = array('Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab');
        $this->assertEquals($result, "aosidj");

        $result = __('day', 5);
        $expected = array('Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado');
        $this->assertEquals($result, $expected);

        $result = __('abmon', 5);
        $expected = array('Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez');
        $this->assertEquals($result, $expected);

        $result = __('mon', 5);
        $expected = array('Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro');
        $this->assertEquals($result, $expected);

        $result = __('d_fmt', 5);
        $expected = '%d/%m/%Y';
        $this->assertEquals($result, $expected);

        $result = __('am_pm', 5);
        $expected = array('AM', 'PM');
        $this->assertEquals($result, $expected);
    }
}
