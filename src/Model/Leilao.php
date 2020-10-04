<?php

namespace Alura\Leilao\Model;

class Leilao
{
    /** @var Lance[] */
    private $lances;
    /** @var string */
    private $descricao;
    private $finalizado;
    public function __construct(string $descricao)
    {
        $this->descricao = $descricao;
        $this->lances = [];
        $this->finalizado = false;
    }

    public function recebeLance(Lance $lance)
    {

        if(!empty($this->lances) && $this->ehDoUltimoUsuario($lance)){
            throw new \DomainException('Usuário não pode propor 2 ofertas seguidas');
        }

        $totalLancesUsuario = $this->quantidadeDeLancesPorUsuario($lance->getUsuario());
        if($totalLancesUsuario >= 5){
            
            throw new \DomainException('Usuário não pode propor mais de 5 ofertas no mesmo leilão');
        }

        $this->lances[] = $lance;
    }

    /**
     * @return Lance[]
     */
    public function getLances(): array
    {
        return $this->lances;
    }

    protected function ehDoUltimoUsuario(Lance $lance): bool
    {
        $ultimoLance = $this->lances[count($this->lances) - 1];
        
        return $lance->getUsuario() ==
        $ultimoLance->getUsuario();
    }

    protected function quantidadeDeLancesPorUsuario(Usuario $usuario): int
    {
        $totalLancesUsuario = array_reduce(
            $this->lances,
            function(int $totalAcumulado,Lance $lanceAtual) use ($usuario)
            {
            if($lanceAtual->getUsuario() == $usuario){
                return $totalAcumulado + 1;
            }
            return $totalAcumulado;
        }, 0);

        return $totalLancesUsuario;
    }
    public function finaliza()
    {
        $this->finalizado = true;
    }

    public function estaFinalizado(): bool
    {
        return $this->finalizado;
    }
}
