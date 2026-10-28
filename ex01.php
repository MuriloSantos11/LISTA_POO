<?php
declare(strict_types=1);

require_once __DIR__ . '/../9-POO/SAPOO/src/Dominio/Titular.php';
require_once __DIR__ . '/../9-POO/SAPOO/src/Dominio/ContaBancaria.php';

class ContaSalario extends ContaBancaria
{
    private int $saquesNoMes = 0;
    private const TAXA_SAQUE = 4.00;

    public function __construct(
        string $numero,
        Titular $titular,
        private readonly string $cnpjEmpregador
    ) {
        parent::__construct($numero, $titular);
    }

    public function depositarDoEmpregador(
        float $valor,
        string $cnpj
    ): void {
        if ($cnpj !== $this->cnpjEmpregador) {
            throw new DomainException('Empregador não autorizado.');
        }

        parent::depositar($valor);
    }

    public function sacar(float $valor): void
    {
        if ($valor <= 0) {
            throw new ValorInvalidoException($valor);
        }

        $taxa = $this->saquesNoMes >= 2
            ? self::TAXA_SAQUE
            : 0.00;

        $total = $valor + $taxa;

        if ($this->saldo < $total) {
            throw new SaldoInsuficienteException(
                $this->saldo,
                $total
            );
        }

        $this->saldo -= $total;
        $this->saquesNoMes++;
    }

    public function transferir(
        float $valor,
        ContaBancaria $destino
    ): void {
        if ($destino->getTitular()->cpf !== $this->titular->cpf) {
            throw new DomainException(
                'Transferência para terceiros não permitida.'
            );
        }

        if ($valor <= 0) {
            throw new ValorInvalidoException($valor);
        }

        if ($this->saldo < $valor) {
            throw new SaldoInsuficienteException(
                $this->saldo,
                $valor
            );
        }

        $this->sacar($valor);
        $destino->depositar($valor);
    }
}

$titular = new Titular(
    'Murilo Santos',
    '12345678901'
);

$conta = new ContaSalario(
    '001',
    $titular,
    '12345678000199'
);

$conta->depositarDoEmpregador(
    1000.00,
    '12345678000199'
);

$conta->sacar(100.00);
$conta->sacar(100.00);
$conta->sacar(100.00);

echo 'Saldo final: R$ ' . number_format(
    $conta->getSaldo(),
    2,
    ',',
    '.'
) . PHP_EOL;
