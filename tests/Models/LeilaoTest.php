<?php

namespace Alura\Leilao\Tests\Model;

use PHPUnit\Framework\TestCase;
use Alura\Leilao\Model\{
    Lance,Leilao,Usuario};

class LeilaoTest extends TestCase
{

    public function testLeilaoNaoDeveReceberLancesRepetidos()
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Usuário não pode propor 2 ofertas seguidas');
        $leilao = new Leilao('Variante');
        $ana = new Usuario('Ana');

        $leilao->recebeLance(new Lance($ana, 1000));
        $leilao->recebeLance(new Lance($ana, 1500));

        static::assertCount(1, $leilao->getLances());
        static::assertEquals(1000, $leilao->getLances()[0]->getValor());

    }
    /**
     * @dataProvider geraLances
     */
    public function testLeilaoDeveReceberLances(
        int $qtdLances,Leilao $leilao, array $valores
    ) {
        
        
        static::assertCount($qtdLances,$leilao->getLances());
        foreach($valores as $i => $valorEsperado){
            static::assertEquals($valorEsperado, $leilao->getLances()[$i]->getValor());
        
        }

    }

    public function testLeilaoNaoDeveAceitarMaisDe5LancesPorUsuario()
    {
        
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Usuário não pode propor mais de 5 ofertas no mesmo leilão');
        $leilao = new Leilao('Mais um carro');
        $joao = new Usuario('joao');
        $maria = new Usuario('Maria');

        $leilao->recebeLance(new Lance($joao,1000));
        $leilao->recebeLance(new Lance($maria,1500));
        $leilao->recebeLance(new Lance($joao,2000));
        $leilao->recebeLance(new Lance($maria,2500));
        $leilao->recebeLance(new Lance($joao,3000));
        $leilao->recebeLance(new Lance($maria,3500));
        $leilao->recebeLance(new Lance($joao,4000));
        $leilao->recebeLance(new Lance($maria,4500));
        $leilao->recebeLance(new Lance($joao,5000));
        $leilao->recebeLance(new Lance($maria,5500));

        $leilao->recebeLance(new Lance($joao,6000));

    }

    public function geraLances()
    {
        $joao = new Usuario('joão');
        $maria = new Usuario('maria');

        $leilao2Lances = new Leilao('carro 2');
        $leilao2Lances->recebeLance(new Lance($joao, 1000));
        $leilao2Lances->recebeLance(new Lance($maria, 2000));

        $leilao1Lance = new Leilao('carro');
        $leilao1Lance->recebeLance(new Lance($maria, 5000));

        return[
            'Leilao 2 lances' => [2, $leilao2Lances, [1000, 2000]],
            'Leilao 1 lance' => [1, $leilao1Lance, [5000]]
        ];
    }
}