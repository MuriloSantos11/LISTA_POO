<?php
declare(strict_types=1);

require_once __DIR__ . '/../9-POO/SAPOO/src/Dominio/Titular.php';
require_once __DIR__ . '/../9-POO/SAPOO/src/Dominio/ContaBancaria.php';

class Transacao
{
    public function __construct(
        public readonly string $tipo,
        public readonly float $valor,
        public readonly DateTimeImmutable $dataHora,
        public readonly bool $sucesso
    ) {
    }
}

class ContaComExtrato extends ContaBancaria
{
    protected array $historico = [];

    private function registrar(string $tipo, float $valor, bool $sucesso): void
    {
        $this->historico[] = new Transacao(
            $tipo,
            $valor,
            new DateTimeImmutable(),
            $sucesso
        );
    }

    public function getExtrato(): array
    {
        return $this->historico;
    }

    public function depositar(float $valor): void
    {
        parent::depositar($valor);
        $this->registrar('Depósito', $valor, true);
    }

    public function sacar(float $valor): void
    {
        if ($valor <= 0) {
            $this->registrar('Saque', $valor, false);
            throw new ValorInvalidoException($valor);
        }

        if ($this->saldo < $valor) {
            $this->registrar('Saque', $valor, false);
            throw new SaldoInsuficienteException($this->saldo, $valor);
        }

        $this->saldo -= $valor;
        $this->registrar('Saque', $valor, true);
    }
}

$titular = new Titular('Murilo Santos', '12345678901');
$conta = new ContaComExtrato('003', $titular);

$conta->depositar(500.00);
$conta->sacar(100.00);

echo "EXTRATO BANCÁRIO\n";
echo "-----------------------------\n";

foreach ($conta->getExtrato() as $transacao) {
    echo $transacao->dataHora->format('d/m/Y H:i:s') . ' | ';
    echo $transacao->tipo . ' | R$ ';
    echo number_format($transacao->valor, 2, ',', '.') . ' | ';
    echo $transacao->sucesso ? "Sucesso\n" : "Falhou\n";
}

echo "Saldo final: R$ " . number_format(
    $conta->getSaldo(), 2, ',', '.'
) . PHP_EOL;
