<?php
declare(strict_types=1);

require_once __DIR__ . '/../9-POO/SAPOO/src/Dominio/Titular.php';
require_once __DIR__ . '/../9-POO/SAPOO/src/Dominio/ContaBancaria.php';
require_once __DIR__ . '/../9-POO/SAPOO/src/Exceptions/ValorInvalidoException.php';
require_once __DIR__ . '/../9-POO/SAPOO/src/Exceptions/SaldoInsuficienteException.php';

class ContaTeste extends ContaBancaria
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

$titular = new Titular('Murilo Santos', '12345678901');
$conta = new ContaTeste('004', $titular);
$violacoes = 0;

echo "=== TESTE DE ENCAPSULAMENTO ===\n\n";

try {
    $conta->{$propriedade} = 999999.00;
    echo "ERRO: foi possível alterar o saldo diretamente.\n";
} catch (Throwable $erro) {
    $violacoes++;
    echo "OK: alteração direta bloqueada.\n";
}

try {
    $contaAbstrata = (new ReflectionClass(ContaBancaria::class))->newInstance('005', $titular);
    echo "ERRO: classe abstrata foi instanciada.\n";
} catch (Throwable $erro) {
    $violacoes++;
    echo "OK: instanciação da classe abstrata bloqueada.\n";
}

try {
    $conta->depositar(-100.00);
    echo "ERRO: depósito negativo foi aceito.\n";
} catch (ValorInvalidoException $erro) {
    $violacoes++;
    echo "OK: depósito negativo bloqueado.\n";
} catch (Throwable $erro) {
    echo "ERRO inesperado: " . $erro->getMessage() . "\n";
}

echo "\nViolações bloqueadas: {$violacoes}/3\n";

if ($violacoes === 3) {
    echo "Todos os testes passaram!\n";
} else {
    echo "Alguns testes precisam ser verificados.\n";
}
