<?php

use Alura\Leilao\Dao\Leilao;
use Alura\Leilao\Infra\ConnectionCreator;

require 'vendor/autoload.php';
$dao = new Leilao(ConnectionCreator::getConnection());

$result = $dao->limpaTabela();

echo ($result) ? "success":"err";

