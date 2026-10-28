<?php
declare(strict_types=1);

require_once __DIR__ . '/../9-POO/SAPOO/src/Dominio/Titular.php';
require_once __DIR__ . '/../9-POO/SAPOO/src/Dominio/ContaBancaria.php';

class ContaCorrente extends ContaBancaria
{
    public function sacar(float $valor): void
    {
        if ($valor <= 0) {
            throw new ValorInvalidoException($valor);
        }

        if ($valor > $this->saldo) {
            throw new SaldoInsuficienteException($this->saldo, $valor);
        }

        $this->saldo -= $valor;
    }
}

class ContaInvestimento extends ContaBancaria
{
    private DateTimeImmutable $dataAplicacao;

    public function __construct(string $numero, Titular $titular)
    {
        parent::__construct($numero, $titular);
        $this->dataAplicacao = new DateTimeImmutable();
    }

    public function sacar(float $valor): void
    {
        if ($valor <= 0) {
            throw new ValorInvalidoException($valor);
        }

        if ($valor > $this->saldo) {
            throw new SaldoInsuficienteException($this->saldo, $valor);
        }

        $this->saldo -= $valor;
    }

    public function taxaAdministracao(): float
    {
        if ($this->saldo >= 10000) {
            return 0.005;
        }

        if ($this->saldo >= 5000) {
            return 0.01;
        }

        return 0.02;
    }

    public function resgatar(float $valor, ContaCorrente $destino): void
    {
        if ($valor <= 0) {
            throw new ValorInvalidoException($valor);
        }

        if ($valor > $this->saldo) {
            throw new SaldoInsuficienteException($this->saldo, $valor);
        }

        $dias = (int) $this->dataAplicacao->diff(
            new DateTimeImmutable()
        )->days;

        $iof = $dias < 30 ? $valor * (0.96 - ($dias * 0.03)) : 0.0;
        $iof = max(0.0, min($valor, $iof));

        $baseIr = $valor - $iof;
        $aliquotaIr = match (true) {
            $dias <= 180 => 0.225,
            $dias <= 360 => 0.20,
            $dias <= 720 => 0.175,
            default => 0.15
        };

        $ir = $baseIr * $aliquotaIr;
        $taxa = $valor * $this->taxaAdministracao();
        $liquido = max(0.0, $valor - $iof - $ir - $taxa);

        $this->sacar($valor);
        $destino->depositar($liquido);

        echo "Valor resgatado: R$ " .
            number_format($valor, 2, ',', '.') . PHP_EOL;
        echo "IOF: R$ " . number_format($iof, 2, ',', '.') . PHP_EOL;
        echo "IR: R$ " . number_format($ir, 2, ',', '.') . PHP_EOL;
        echo "Administração: R$ " .
            number_format($taxa, 2, ',', '.') . PHP_EOL;
        echo "Valor creditado: R$ " .
            number_format($liquido, 2, ',', '.') . PHP_EOL;
    }
}

$titular = new Titular('Murilo Santos', '12345678901');

$corrente = new ContaCorrente('006', $titular);
$investimento = new ContaInvestimento('007', $titular);

$investimento->depositar(5000.00);

$investimento->resgatar(1000.00, $corrente);

echo PHP_EOL . 'Saldo do investimento: R$ ' .
    number_format($investimento->getSaldo(), 2, ',', '.') . PHP_EOL;

echo 'Saldo da conta corrente: R$ ' .
    number_format($corrente->getSaldo(), 2, ',', '.') . PHP_EOL;
