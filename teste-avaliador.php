<?php

use Alura\Leilao\Model\{
    Lance,Leilao,Usuario};
use Alura\Leilao\Service\Avaliador;

require 'vendor/autoload.php';

$leilao = new Leilao('carro');

$maria = new Usuario('Maria');

$joao = new Usuario('João');

$leilao->recebeLance(new Lance($joao, 2000));

$leilao->recebeLance(new Lance($maria, 3000));

$leiloeiro = new Avaliador();
$leiloeiro->avalia($leilao);

$maiorValor = $leiloeiro->getMaiorValor();

echo $maiorValor;