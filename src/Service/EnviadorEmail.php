<?php

namespace Alura\Leilao\Service;

use Alura\Leilao\Model\Leilao;

class EnviadorEmail
{

    public function notificarTerminoLeilao(Leilao $Leilao)
    {
        $sucesso = mail('usuario@email.com','Leilao finalizado',
        'O leilão para'.$Leilao->recuperarDescricao().
        'foi finalizado!');
        if(!$sucesso){
            throw new \DomainException('Erro ao enviar o e-mail');
        }
    }

}