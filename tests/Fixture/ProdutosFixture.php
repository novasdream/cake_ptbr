<?php
/**
 * Fixture de Produto
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @author        Juan Basso <jrbasso@gmail.com>
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
namespace CakePtbr\Test\Fixture;
use Cake\TestSuite\Fixture\TestFixture;


/**
 * Produto Fixture
 *
 */
class ProdutosFixture extends TestFixture
{

    /**
     * Nome do fixture
     *
     * @var string
     * @access public
     */
    var $name = 'Produto';

    /**
     * Campos da tabela
     *
     * @var array
     * @access public
     */
    var $fields = [
        'id' => ['type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'],
        'nome' => ['type' => 'string', 'null' => false, 'default' => NULL],
        'valor' => ['type' => 'float', 'null' => false, 'default' => NULL],
        'indexes' => ['PRIMARY' => ['column' => 'id', 'unique' => 1]],
        'tableParameters' => ['charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB']
    ];

    /**
     * Registros
     *
     * @var array
     * @access public
     */
    var $records = [
        [
            'id' => 1,
            'nome' => 'Produto 1',
            'valor' => 1.99
        ],
        [
            'id' => 2,
            'nome' => 'Produto 2',
            'valor' => 1000.20
        ],
        [
            'id' => 3,
            'nome' => 'Produto 3',
            'valor' => 1999000.00
        ]
    ];
}
