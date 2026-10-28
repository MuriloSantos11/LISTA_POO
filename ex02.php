<?php
declare(strict_types=1);

require_once __DIR__ . '/../9-POO/SAPOO/src/Dominio/Titular.php';
require_once __DIR__ . '/../9-POO/SAPOO/src/Dominio/ContaBancaria.php';
interface NotificadorInterface
{
    public function notificar(
        Titular $titular,
        string $mensagem
    ): bool;
}

class NotificadorEmail implements NotificadorInterface
{
    public function notificar(
        Titular $titular,
        string $mensagem
    ): bool {
        echo "E-mail para {$titular->nome}: {$mensagem}\n";
        return true;
    }
}

class NotificadorSMS implements NotificadorInterface
{
    public function notificar(
        Titular $titular,
        string $mensagem
    ): bool {
        echo "SMS para {$titular->nome}: {$mensagem}\n";
        return true;
    }
}

class NotificadorWhatsApp implements NotificadorInterface
{
    public function notificar(
        Titular $titular,
        string $mensagem
    ): bool {
        echo "WhatsApp para {$titular->nome}: {$mensagem}\n";
        return true;
    }
}

class ContaCorrenteNotificavel extends ContaBancaria
{
    private array $notificadores = [];

    public function adicionarNotificador(
        NotificadorInterface $notificador
    ): void {
        $this->notificadores[] = $notificador;
    }

    private function enviarNotificacao(string $mensagem): void
    {
        foreach ($this->notificadores as $notificador) {
            $notificador->notificar($this->titular, $mensagem);
        }
    }

    public function sacar(float $valor): void
    {
        if ($valor <= 0) {
            throw new ValorInvalidoException($valor);
        }

        if ($this->saldo < $valor) {
            throw new SaldoInsuficienteException(
                $this->saldo,
                $valor
            );
        }

        $this->saldo -= $valor;
        $this->enviarNotificacao(
            "Saque de R$ " . number_format($valor, 2, ',', '.') .
            ". Saldo: R$ " . number_format($this->saldo, 2, ',', '.')
        );
    }

    public function depositar(float $valor): void
    {
        parent::depositar($valor);

        $this->enviarNotificacao(
            "Depósito de R$ " . number_format($valor, 2, ',', '.') .
            ". Saldo: R$ " . number_format($this->saldo, 2, ',', '.')
        );
    }
}

$titular = new Titular('Murilo Santos', '12345678901');

$conta = new ContaCorrenteNotificavel('002', $titular);

$conta->adicionarNotificador(new NotificadorEmail());
$conta->adicionarNotificador(new NotificadorSMS());
$conta->adicionarNotificador(new NotificadorWhatsApp());

$conta->depositar(500.00);
$conta->sacar(100.00);

echo "\nSaldo final: R$ " . number_format(
    $conta->getSaldo(),
    2,
    ',',
    '.'
) . PHP_EOL;
