<?php
/**
 * Behavior de acesso a serviços dos Correios
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @author        Juan Basso <jrbasso@gmail.com>
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
namespace CakePtbr\Model\Behavior;

use Cake\Core\Configure;
use Cake\Network\Http\Client;
use Cake\ORM\Behavior;
use Cake\Utility\Xml;

/**
 * CorreiosBehavior
 *
 * @link http://wiki.github.com/jrbasso/cake_ptbr/behavior-correios
 */
trait CorreiosTrait
{


    // Tipo de frete
    protected $CORREIOS_SEDEX = 40010;
    protected $CORREIOS_SEDEX_A_COBRAR = 40045;
    protected $CORREIOS_SEDEX_10 = 40215;
    protected $CORREIOS_SEDEX_HOJE = 40290;
    protected $CORREIOS_E_SEDEX = 81019;
    protected $CORREIOS_ENCOMENDA_NORMAL = 41017;
    protected $CORREIOS_PAC = 41106;

    // Erros
    protected $ERRO_CORREIOS_PARAMETROS_INVALIDOS = -1000;
    protected $ERRO_CORREIOS_EXCESSO_PESO = -1001;
    protected $ERRO_CORREIOS_FALHA_COMUNICACAO = -1002;
    protected $ERRO_CORREIOS_CONTEUDO_INVALIDO = -1003;


    /**
     * Cálculo do valor do frete
     *
     * @param object $model Instancia de entidade
     * @param int $servico Código do serviço, ver as defines CORREIOS_*
     * @param string $cepOrigem CEP de origem no formato XXXXX-XXX
     * @param string $cepDestino CEP de destino no formato XXXXX-XXX
     * @param float $peso Peso do pacote, em quilos
     * @param bool $maoPropria Usar recurso de mão própria?
     * @param float $valorDeclarado Valor declarado do pacote
     * @param bool $avisoRecebimento Aviso de recebimento?
     * @return mixed Array com os dados do frete ou integer com erro. Ver defines ERRO_CORREIOS_* para erros.
     * @access public
     */
    public function valorFrete(&$model, $servico, $cepOrigem, $cepDestino, $peso, $maoPropria = false, $valorDeclarado = 0.0, $avisoRecebimento = false)
    {
        // Validação dos parâmetros
        if ($this->__erroCorreios($this->__validaParametros($servico, $cepOrigem, $cepDestino, $peso, $valorDeclarado))) {
            return $this->__validaParametros($servico, $cepOrigem, $cepDestino, $peso, $valorDeclarado);
        }

        // Ajustes nos parâmetros
        $maoPropria = $maoPropria ? 'S' : 'N';
        $avisoRecebimento = $avisoRecebimento ? 'S' : 'N';

        $query = [
            'resposta' => 'xml',
            'servico' => $servico,
            'cepOrigem' => $cepOrigem,
            'cepDestino' => $cepDestino,
            'peso' => $peso,
            'MaoPropria' => $maoPropria,
            'valorDeclarado' => $valorDeclarado,
            'avisoRecebimento' => $avisoRecebimento
        ];
        $retornoCorreios = $this->_requisitaUrl('/encomendas/precos/calculo.cfm', 'get', $query);
        if (is_integer($retornoCorreios)) {
            return $retornoCorreios;
        }
        $xml = Xml::build($retornoCorreios);
        $infoCorreios = Xml::toArray($xml);
        if (!isset($infoCorreios['CalculoPrecos']['DadosPostais'])) {
            return $this->ERRO_CORREIOS_CONTEUDO_INVALIDO;
        }
        extract($infoCorreios['CalculoPrecos']['DadosPostais']);
        return [
            'ufOrigem' => $uf_origem,
            'ufDestino' => $uf_destino,
            'capitalOrigem' => ($local_origem == 'Capital'),
            'capitalDestino' => ($local_destino == 'Capital'),
            'valorMaoPropria' => $mao_propria,
            'valorTarifaValorDeclarado' => $tarifa_valor_declarado,
            'valorFrete' => ($preco_postal - $tarifa_valor_declarado - $mao_propria),
            'valorTotal' => $preco_postal
        ];
    }

    /**
     * Verifica se o valor é um erro.
     * @param int $valor Valor a ser verificado
     * @return bool
     */
    private function __erroCorreios($valor)
    {
        $todosErros = [
            $this->ERRO_CORREIOS_CONTEUDO_INVALIDO,
            $this->ERRO_CORREIOS_EXCESSO_PESO,
            $this->ERRO_CORREIOS_FALHA_COMUNICACAO,
            $this->ERRO_CORREIOS_PARAMETROS_INVALIDOS
        ];
        return in_array($valor, $todosErros);
    }

    /**
     * Verifica se os parametros são válidos
     * @param int $servico Código do serviço, ver as variáveis deste trait começando por CORREIOS_*
     * @param string $cepOrigem Cep do remetente
     * @param string $cepDestino Cep do destinatário
     * @param float $peso Peso do pacote
     * @param float $valorDeclarado Valor declarado do pacote
     * @return int
     */
    private function __validaParametros($servico, $cepOrigem, $cepDestino, $peso, $valorDeclarado)
    {
        $tipos = [$this->CORREIOS_SEDEX, $this->CORREIOS_SEDEX_A_COBRAR, $this->CORREIOS_SEDEX_10, $this->CORREIOS_SEDEX_HOJE, $this->CORREIOS_ENCOMENDA_NORMAL];
        if (!in_array($servico, $tipos)) {
            return $this->ERRO_CORREIOS_PARAMETROS_INVALIDOS;
        }
        if (!$this->_validaCep($cepOrigem) || !$this->_validaCep($cepDestino)) {
            return $this->ERRO_CORREIOS_PARAMETROS_INVALIDOS;
        }
        if (!is_numeric($peso) || !is_numeric($valorDeclarado)) {
            return $this->ERRO_CORREIOS_PARAMETROS_INVALIDOS;
        }
        if ($peso > 30.0) {
            return $this->ERRO_CORREIOS_EXCESSO_PESO;
        } elseif ($peso < 0.0) {
            return $this->ERRO_CORREIOS_PARAMETROS_INVALIDOS;
        }
        if ($valorDeclarado < 0.0) {
            return $this->ERRO_CORREIOS_PARAMETROS_INVALIDOS;
        }

        return 0;
    }

    /**
     * Verificar se o CEP digitado está correto
     *
     * @param string $cep CEP
     * @return boolean CEP Correto
     * @access protected
     */
    protected function _validaCep($cep)
    {
        return (bool)preg_match('/^\d{5}\-?\d{3}$/', $cep);
    }

    /**
     * Requisita dados dos Correios
     *
     * @param string $url Caminho relativo da página nos Correios
     * @param string $method Método de requisição (POST/GET)
     * @param array $query Dados para enviar na página
     * @return string Página solicitada
     * @access protected
     */
    protected function _requisitaUrl($url, $method, $query)
    {
        $defaultHeader = [
            'Origin' => 'http://www.correios.com.br',
            'Referer' => 'http://www.correios.com.br/encomendas/prazo/default.cfm',
            'User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/535.1 (KHTML, like Gecko) Ubuntu/11.04 Chromium/14.0.835.202 Chrome/14.0.835.202 Safari/535.1'
        ];
        $httpClient = new Client(['request' => ['header' => $defaultHeader]]);
        $uri = [
            'scheme' => 'http',
            'host' => 'www.correios.com.br',
            'port' => 80,
            'path' => $url
        ];
        if ($method === 'get') {
            $response = trim($httpClient->get($uri, $query));
        } else {
            $response = $httpClient->post($uri, $query);
        }
        if (!$response->isOk()) {
            return $this->ERRO_CORREIOS_FALHA_COMUNICACAO;
        }
        return trim($response->body());
    }

    /**
     * Pegar o endereço de um CEP específico
     *
     * @param object $model Model
     * @param string $cep CEP no format XXXXX-XXX
     * @return mixed Array com os dados do endereço ou interger para erro. Ver defines ERRO_CORREIOS_* para os erros.
     * @access public
     */
    public function endereco(&$model, $cep)
    {
        if (!$this->_validaCep($cep, '-')) {
            return $this->ERRO_CORREIOS_PARAMETROS_INVALIDOS;
        }

        $data = [
            'resposta' => 'paginaCorreios',
            'data' => date('d/m/Y'),
            'dataAtual' => date('d/m/Y'),
            'servico' => $this->CORREIOS_SEDEX,
            'cepOrigem' => $cep,
            'cepDestino' => $cep,
            'peso' => 1,
            'MaoPropria' => 'N',
            'valorDeclarado' => '',
            'avisoRecebimento' => 'N',
            'Altura' => '',
            'Comprimento' => '',
            'Diametro' => '',
            'Formato' => 1,
            'Largura' => '',
            'embalagem' => 116600055,
            'valorD' => ''
        ];
        $retornoCorreios = $this->_requisitaUrl('/encomendas/prazo/prazo.cfm', 'post', $data);
        if (is_integer($retornoCorreios)) {
            return $retornoCorreios;
        }

        // Convertendo para o encoding da aplicação. Isto só funciona se a extensão multibyte estiver ativa
        $encoding = Configure::read('App.encoding');
        if (function_exists('mb_convert_encoding') && $encoding != null && strcasecmp($encoding, 'iso-8859-1') != 0) {
            $retornoCorreios = mb_convert_encoding($retornoCorreios, $encoding, 'ISO-8859-1');
        }
        // Checar se o conteúdo está lá e reduzir o escopo de busca dos valores
        if (!preg_match('/\<b\>CEP:\<\/b\>(.*)\<b\>Prazo de Entrega/sm', $retornoCorreios, $matches)) {
            return $this->ERRO_CORREIOS_CONTEUDO_INVALIDO;
        }
        $escopoReduzido = $matches[1];
        // Logradouro
        preg_match('/\<b\>Endere&ccedil;o:\<\/b\>\s*\<\/td\>\s*\<td[^\>]*>([^\<]*)\</', $escopoReduzido, $matches);
        $logradouro = $matches[1];
        // Bairro
        preg_match('/\<b\>Bairro:\<\/b\>\s*\<\/td\>\s*\<td[^\>]*>([^\<]*)\</', $escopoReduzido, $matches);
        $bairro = $matches[1];
        // Cidade e Estado
        preg_match('/\<b\>Cidade\/UF:\<\/b\>\s*\<\/td\>\s*\<td[^\>]*>([^\<]*)\</', $escopoReduzido, $matches);
        list($cidade, $uf) = explode('/', $matches[1]);

        $result = compact('logradouro', 'bairro', 'cidade', 'uf');
        return array_map('trim', $result);
    }
}
