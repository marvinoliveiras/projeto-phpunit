<?php
namespace Alura\Leilao\Tests\Integration\Dao;

use Alura\Leilao\Dao\Leilao as LeilaoDao;
use Alura\Leilao\Infra\ConnectionCreator;
use Alura\Leilao\Model\Leilao;
use PHPUnit\Framework\TestCase;

class LeilaoDaoTest extends TestCase
{

    private static $pdo;

    public static function setUpBeforeClass(): void
    {
        
        self::$pdo = new \PDO('sqlite::memory:');
        self::$pdo->exec('create table leiloes(
            id INTEGER primary Key, descricao TEXT,
            finalizado BOOL, dataInicio TEXT);'
        );
    }
    public function setUp(): void
    {
        self::$pdo->beginTransaction();
    }
    /**
     *@dataProvider leiloes
     */
    public function testBuscaLeiloesNaoFinalizados(array $leiloes)
    {
        $leilaoDao = new LeilaoDao(self::$pdo);

        foreach($leiloes as $leilao){
            $leilaoDao->salva($leilao);
        }
        $leiloes = $leilaoDao->recuperarNaoFinalizados();

        self::assertCount(1, $leiloes);
        self::assertContainsOnlyInstancesOf(Leilao::class, $leiloes);
        self::assertSame('Variante', $leiloes[0]->recuperarDescricao());
    }
    /**
     *@dataProvider leiloes
     */
    public function testBuscaLeiloesFinalizados(array $leiloes)
    {
        $leilaoDao = new LeilaoDao(self::$pdo);

        foreach($leiloes as $leilao){
            $leilaoDao->salva($leilao);
        }
        $leiloes = $leilaoDao->recuperarFinalizados();

        self::assertCount(1, $leiloes);
        self::assertContainsOnlyInstancesOf(Leilao::class, $leiloes);
        self::assertSame('Fiat 147', $leiloes[0]->recuperarDescricao());
    }

    public function testAoAtualizarLeilaoStatusDeveSerAlterado()
    {
        $leilao = new Leilao('Brasília');
        $leilaoDao = new LeilaoDao(self::$pdo);
        $leilao = $leilaoDao->salva($leilao);
        $leilao->finaliza();

        $leilaoDao->atualiza($leilao);

        $leiloes = $leilaoDao->recuperarFinalizados();
        self::assertCount(1, $leiloes);
        self::assertSame('Brasília', $leiloes[0]->recuperarDescricao());
    }

    public function leiloes()
    {
        $naoFinalizado = new Leilao('Variante');
        $finalizado = new Leilao('Fiat 147');
        $finalizado->finaliza();
        return[
            [
                [$naoFinalizado, $finalizado]
            ]
        ];
    }
    public function tearDown(): void
    {
        //$this->pdo->exec("DELETE FROM leiloes WHERE id IS NOT NULL");
        self::$pdo->rollBack();
    }
}