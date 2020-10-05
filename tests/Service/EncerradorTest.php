<?php
namespace Alura\Leilao\Tests\Service;

use Alura\Leilao\Dao\Leilao as LeilaoDao;
use Alura\Leilao\Model\Leilao;
use Alura\Leilao\Service\Encerrador;
use Alura\Leilao\Service\EnviadorEmail;
use PHPUnit\Framework\TestCase;
/*class LeilaoDaoMock extends LeilaoDao
{
    private $leiloes = [];
    public function salva(Leilao $leilao): void
    {
        $this->leiloes[] = $leilao;
    }
    public function recuperarNaoFinalizados(): array
    {
        return array_filter($this->leiloes, function(Leilao $leilao){
            return !$leilao->estaFinalizado();
        });
    }
    public function recuperarFinalizados(): array
    {
        return array_filter($this->leiloes, function(Leilao $leilao){
            return $leilao->estaFinalizado();
        });
    }
    public function atualiza(Leilao $leilao)
    {
        
    }
}*/
class EncerradorTest extends TestCase
{
    private $encerrador;
    /**@var MockObject */
    private $enviadorEmail;
    private $leilaoFiat147;
    private $leilaoVariante;

    protected function setUp(): void
    {
        $this->leilaoFiat147 = new Leilao('Fiat 147', new \DateTimeImmutable('8 days ago'));
        $this->leilaoVariante = new Leilao('Variant', new \DateTimeImmutable('10 days ago'));

        $leilaoDao = $this->getMockBuilder(LeilaoDao::class)
            ->setConstructorArgs([new \PDO('sqlite::memory:')])
            ->getMock();
        $leilaoDao->method('recuperarNaoFinalizados')
            ->willReturn([$this->leilaoFiat147, $this->leilaoVariante]);

        $leilaoDao->method('recuperarFinalizados')
            ->willReturn([$this->leilaoFiat147, $this->leilaoVariante]);

        $leilaoDao->expects($this->exactly(2))->method('atualiza')->withConsecutive([$this->leilaoFiat147], [$this->leilaoVariante]);

        $this->enviadorEmail = $this->createMock(EnviadorEmail::class);
        $this->encerrador = new Encerrador($leilaoDao, $this->enviadorEmail);
    }

    public function testLeiloesComMaisDeUmaSemanaDevemSerEncerrados()
    {
        $this->encerrador->encerra();

        $leiloes = [$this->leilaoFiat147, $this->leilaoVariante];

        self::assertCount(2, $leiloes);
        self::assertTrue( $leiloes[0]->estaFinalizado());
        self::assertTrue($leiloes[1]->estaFinalizado());


    }

    public function testProcessoDeEncerramentoDeveContinuarMesmoOcorrendoErro()
    {
        $e = new \DomainException('Erro ao enviar o e-mail');
        $this->enviadorEmail->expects($this->exactly(2))
            ->method('notificarTerminoLeilao')
            ->willThrowException($e);
        $this->encerrador->encerra();
    }

    public function testsoDeveEnviarPorEmailAposFinalizado()
        {
            $this->enviadorEmail->expects($this->exactly(2))
                ->method('notificarTerminoLeilao')
                ->willReturnCallback(function(Leilao $leilao){
                    static::assertTrue($leilao->estaFinalizado());
                });

            $this->encerrador->encerra();
        }
}