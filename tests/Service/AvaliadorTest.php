<?php
namespace Alura\Tests;

use PHPUnit\Framework\TestCase;
use Alura\Leilao\Model\{
    Lance,Leilao,Usuario};
use Alura\Leilao\Service\Avaliador;
use DomainException;

class AvaliadorTest extends TestCase
{
    /**@var Avaliador */
    private $leiloeiro;

    protected function setUp(): void
    {
        $this->leiloeiro = new Avaliador();
    }
    /**
     * @dataProvider LeilaoEmOrdemCrescente
     * @dataProvider LeilaoEmOrdemDecrescente
     * @dataProvider LeilaoEmOrdemAleatoria
     */

    public function testMaiorValor(Leilao $leilao)
    {
        //Arrang - Given

        //Act - When
        $this->leiloeiro->avalia($leilao);
        $maiorValor = $this->leiloeiro->getMaiorValor();
        //assert - Then
        self::assertEquals(2000, $maiorValor);
    }
    /**
     * @dataProvider LeilaoEmOrdemCrescente
     * @dataProvider LeilaoEmOrdemDecrescente
     * @dataProvider LeilaoEmOrdemAleatoria
     */
    public function testMenorValor(Leilao $leilao)
    {
        //Act - When
        $this->leiloeiro->avalia($leilao);
        
        $menorValor = $this->leiloeiro->getMenorValor();
        //assert - Then
        self::assertEquals(1000, $menorValor);

    }

    /**
     * @dataProvider LeilaoEmOrdemCrescente
     * @dataProvider LeilaoEmOrdemDecrescente
     * @dataProvider LeilaoEmOrdemAleatoria
     */
    public function test3MaioresValores(Leilao $leilao)
    {
        $this->leiloeiro->avalia($leilao);
        $maiores = $this->leiloeiro->getMaioresLances();
        static::assertCount(3, $maiores);
        static::assertEquals(2000,$maiores[0]->getValor());
        static::assertEquals(1700,$maiores[1]->getValor());
        static::assertEquals(1500,$maiores[2]->getValor());

    }

    public function testLeilaoVazioNaoPodeSerAvaliado()
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Não é possível avaliar leilão vazio!');

        $leilao = new Leilao('Fusca Azul');

        $this->leiloeiro->avalia($leilao);



    }

    public function testLeilaoFinalizadoNaoPodeSerAvaliado()
        {
          $this->expectException(\DomainException::class);
          $this->expectExceptionMessage('Leilão já finalizado');

          $leilao = new Leilao('Outro carro');
          $leilao->recebeLance(new Lance(new Usuario('Marcos'), 2000));
          $leilao->finaliza();

          $this->leiloeiro->avalia($leilao);

          
        }

    public function leilaoEmOrdemCrescente()
    {
        $leilao = new Leilao('carro');

        $joao = new Usuario('Joao');
        $maria = new Usuario('maria');
        $pedro = new Usuario('pedro');
        $jorge = new Usuario('jorge');

        $leilao->recebeLance(new Lance($maria,1000));
        $leilao->recebeLance(new Lance($joao,1500));
        $leilao->recebeLance(new Lance($jorge,1700));
        $leilao->recebeLance(new Lance($pedro,2000));

        return ['ordemCrescente' => [$leilao]];
    }

    public function leilaoEmOrdemDecrescente()
    {
        $leilao = new Leilao('carro');

        $joao = new Usuario('Joao');
        $maria = new Usuario('maria');
        $pedro = new Usuario('pedro');
        $jorge = new Usuario('jorge');

        $leilao->recebeLance(new Lance($pedro,2000));
        $leilao->recebeLance(new Lance($jorge,1700));
        $leilao->recebeLance(new Lance($joao,1500));
        $leilao->recebeLance(new Lance($maria,1000));

        return ['ordemDecrescente' => [$leilao]];
    }
    public function leilaoEmOrdemAleatoria()
    {
        $leilao = new Leilao('carro');

        $joao = new Usuario('Joao');
        $maria = new Usuario('maria');
        $pedro = new Usuario('pedro');
        $jorge = new Usuario('jorge');

        $leilao->recebeLance(new Lance($jorge,1700));
        $leilao->recebeLance(new Lance($maria,1000));
        $leilao->recebeLance(new Lance($pedro,2000));
        $leilao->recebeLance(new Lance($joao,1500));

        return ['ordemAleatoria' => [$leilao]];
    }

    /*public function entregaLeiloes()
    {
        return [
            [$this->leilaoEmOrdemCrescente()],
            [$this->leilaoEmOrdemDecrescente()],
            [$this->leilaoEmOrdemAleatoria()]
        ];
    }*/
}