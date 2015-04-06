<?php
/**
 * Testes do helper de formatação
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @author        Juan Basso <jrbasso@gmail.com>
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
namespace CakePtbr\Test\TestCase\View\Helper;

use Cake\I18n\I18n;
use Cake\I18n\Time;
use Cake\View\Helper;
use Cake\View\Helper\NumberHelper;
use Cake\View\Helper\TimeHelper;
use CakePtbr\View\Helper\FormatacaoHelper;
use Cake\Controller\Controller;
use Cake\TestSuite\TestCase;
use Cake\View\View;
use Carbon\Carbon;


/**
 * Formatacao Test Case
 *
 */
class FormatacaoHelperTeste extends TestCase
{

    /**
     * Formatação
     *
     * @var FormatacaoHelper $Formatacao
     * @access public
     */
    public $Formatacao = null;

    /**
     * setUp
     *
     * @retun void
     * @access public
     */
    public function setUp()
    {
        parent::setUp();
        $this->Formatacao = new FormatacaoHelper(new View());
        date_default_timezone_set('America/Sao_Paulo');
        I18n::locale('pt_BR');
        setlocale(LC_TIME, ['pt_BR.uft8', 'pt_BR', 'Português', 'Portuguese', 'pt_BR.UTF-8']);
    }

    /**
     * testData
     *
     * @retun void
     * @access public
     */
    public function testData()
    {
        $this->assertEquals(date('d/m/Y'), $this->Formatacao->data());
        $this->assertEquals('21/04/2009', $this->Formatacao->data(strtotime('2009-04-21')));
        $this->assertEquals('21/04/2009', $this->Formatacao->data('2009-04-21'));
        $this->assertEquals('Inválido', $this->Formatacao->data('errado', ['invalid' => 'Inválido']));
        $this->assertEquals('20/04/2009', $this->Formatacao->data(strtotime('2009-04-21 00:00:00 GMT'), array('userOffset' => 'America/Sao_Paulo')));
        $this->assertEquals('20/04/2009', $this->Formatacao->data('2009-04-21 00:00:00 GMT', array('userOffset' => 'America/Sao_Paulo')));
    }

    /**
     * testDataHora
     *
     * @retun void
     * @access public
     */
    public function testDataHora()
    {
        $this->assertEquals(date('d/m/Y H:i:s'), $this->Formatacao->dataHora());
        $this->assertEquals(date('d/m/Y H:i'), $this->Formatacao->dataHora(null, false));
        $this->assertEquals('21/04/2009 10:20:30', $this->Formatacao->dataHora(strtotime('2009-04-21 10:20:30')));
        $this->assertEquals('21/04/2009 10:20', $this->Formatacao->dataHora(strtotime('2009-04-21 10:20:30'), false));
        $this->assertEquals('21/04/2009 13:20', $this->Formatacao->dataHora('2009-04-21 10:20:30', false));
        $this->assertEquals('Inválido', $this->Formatacao->dataHora('errado', true, ['invalid' => 'Inválido']));
        $this->assertEquals('21/04/2009 08:20', $this->Formatacao->dataHora(strtotime('2009-04-21 10:20:30 GMT'), false, array('userOffset' => 'Etc/GMT+2')));
        $this->assertEquals('21/04/2009 12:20', $this->Formatacao->dataHora(strtotime('2009-04-21 10:20:30 GMT'), false, array('userOffset' => 'Etc/GMT-2')));
        $this->assertEquals('21/04/2009 08:20', $this->Formatacao->dataHora('2009-04-21 10:20:30 GMT', false, array('userOffset' => 'Etc/GMT+2')));
    }

    /**
     * testDataCompleta
     *
     * @retun void
     * @access public
     */
    public function testDataCompleta()
    {
        $this->assertEquals('Terça-feira, 21 de Abril de 2009, 10:20:30', $this->Formatacao->dataCompleta(strtotime('2009-04-21 10:20:30')));
        $this->assertEquals('Terça-feira, 21 de Abril de 2009, 10:20:30', $this->Formatacao->dataCompleta('2009-04-21 10:20:30'));
    }

    /**
     * testTempo
     *
     * @return void
     * @access public
     */
    public function testTempo()
    {
        $this->assertEquals('menos de 1 minuto', $this->Formatacao->tempo());
        $this->assertEquals('1 hora', $this->Formatacao->tempo(strtotime('-1 hour')));
        $this->assertEquals('1 hora', $this->Formatacao->tempo('-1 hour'));
    }

    /**
     * testPrecisao
     *
     * @retun void
     * @access public
     */
    public function testPrecisao()
    {
        $this->assertEquals($this->Formatacao->precisao(-10), '-10,000');
        $this->assertEquals($this->Formatacao->precisao(0), '0,000');
        $this->assertEquals($this->Formatacao->precisao(10), '10,000');
        $this->assertEquals($this->Formatacao->precisao(10.323), '10,323');
        $this->assertEquals($this->Formatacao->precisao(10.56486), '10,565');
        $this->assertEquals($this->Formatacao->precisao(10.56486, 2), '10,56');
        $this->assertEquals($this->Formatacao->precisao(10.56486, 0), '11');
    }

    /**
     * testPorcentagem
     *
     * @retun void
     * @access public
     */
    public function testPorcentagem()
    {
        $this->assertEquals($this->Formatacao->porcentagem(-10), '-10,00%');
        $this->assertEquals($this->Formatacao->porcentagem(0), '0,00%');
        $this->assertEquals($this->Formatacao->porcentagem(10), '10,00%');
        $this->assertEquals($this->Formatacao->porcentagem(10, 1), '10,0%');
        $this->assertEquals($this->Formatacao->porcentagem(10.123), '10,12%');
        $this->assertEquals($this->Formatacao->porcentagem(10, 0), '10%');
    }

    /**
     * testMoeda
     *
     * @retun void
     * @access public
     */
    public function testMoeda()
    {
        $this->assertEquals($this->Formatacao->moeda(-10), '-R$10,00');
        $this->assertEquals($this->Formatacao->moeda(-10.12), '-R$10,12');
        $this->assertEquals($this->Formatacao->moeda(-0.12), '-R$0,12');
        $this->assertEquals($this->Formatacao->moeda(0), 'R$0,00');
        $this->assertEquals($this->Formatacao->moeda(0.5), 'R$0,50');
        $this->assertEquals($this->Formatacao->moeda(0.52), 'R$0,52');
        $this->assertEquals($this->Formatacao->moeda(10), 'R$10,00');
        $this->assertEquals($this->Formatacao->moeda(10.12), 'R$10,12');
    }

    /**
     * testMoedaPorExtenso
     *
     * @retun void
     * @access public
     */
    public function testMoedaPorExtenso()
    {
        $this->assertEquals($this->Formatacao->moedaPorExtenso(0), 'zero');
        $this->assertEquals($this->Formatacao->moedaPorExtenso(0.52), 'cinquenta e dois centavos');
        $this->assertEquals($this->Formatacao->moedaPorExtenso(1), 'um real');
        $this->assertEquals($this->Formatacao->moedaPorExtenso(1.2), 'um real e vinte centavos');
        $this->assertEquals($this->Formatacao->moedaPorExtenso(10), 'dez reais');
        $this->assertEquals($this->Formatacao->moedaPorExtenso(15), 'quinze reais');
        $this->assertEquals($this->Formatacao->moedaPorExtenso(25), 'vinte e cinco reais');
        $this->assertEquals($this->Formatacao->moedaPorExtenso(40), 'quarenta reais');
        $this->assertEquals($this->Formatacao->moedaPorExtenso(100), 'cem reais');
        $this->assertEquals($this->Formatacao->moedaPorExtenso(105), 'cento e cinco reais');
        $this->assertEquals($this->Formatacao->moedaPorExtenso(120), 'cento e vinte reais');
        $this->assertEquals($this->Formatacao->moedaPorExtenso(210), 'duzentos e dez reais');
        $this->assertEquals($this->Formatacao->moedaPorExtenso(322), 'trezentos e vinte e dois reais');
        $this->assertEquals($this->Formatacao->moedaPorExtenso(1234), 'um mil duzentos e trinta e quatro reais');
        $this->assertEquals($this->Formatacao->moedaPorExtenso(100000), 'cem mil reais');
        $this->assertEquals($this->Formatacao->moedaPorExtenso(1000000), 'um milhão de reais');
        $this->assertEquals($this->Formatacao->moedaPorExtenso(1045763), 'um milhão, quarenta e cinco mil setecentos e sessenta e três reais');
    }
}
