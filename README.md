## 9. LISTA DE EXERCÍCIOS DE FIXAÇÃO E PRÁTICA

### Parte A: Questões Teóricas de Fixação

1. **Procedural vs. OO:** Quais são os três principais riscos técnicos de manter regras financeiras críticas implementadas puramente através de arrays associativos e scripts procedurais?

R. Manter regras financeiras críticas implementadas puramente através de scripts procedurais e arrays associativos expõe sistemas de missão crítica a sérios problemas de estabilidade e integridade dos dados, Ocupam memoria desnecessaria, não tem como deixar privado ou protegido as classes ou seus atributos, em uma manutenção, teria de alterar o código inteiro e ainda tem a margem do erro de "arrumar a parte errada" e também permitir que outras variavel herdem caracterirsticas dessa classe abstrata.

2. **Ciclo de Vida do Objeto:** O que ocorre na memória Heap do servidor quando a instrução `new ContaCorrente(...)` é executada? Qual é o papel do método `__construct()` nesse processo?

R.  O PHP aloca espaço na memória Heap para a nova instância. O método `__construct()` é chamado imediatamente a seguir para inicializar o estado do objeto (atribuir dependências e valores iniciais), garantindo que ele nasça válido e pronto para uso.

3. **A Pseudo-Variável `$this`:** Explique por que o comando `$this->saldo` é obrigatório para acessar um atributo dentro da classe, diferenciando-o de uma variável local `$saldo`.

R. O `$this->saldo` serve para acessar o saldo que pertence ao objeto. Já o `$saldo` sozinho é uma variável comum, que pode existir apenas dentro de um método. Então, usamos o `$this` para mostrar que estamos trabalhando com o atributo do próprio objeto, e não com uma variável separada.

```php
class Finaceiro {
    //atributo
    private float $saldo;

    //método de Acesso
    public function setSaldo(float $valor): void{
        $saldo = $valor;
        $this->saldo += $saldo;
    }
}
financeiro->setSaldo(20.00)
```


4. **Encapsulamento e Invariantes:** Por que um desenvolvedor sênior de back-end nunca deve criar um método `public function setSaldo(float $novoSaldo)` para uma conta bancária?

R. Devemos evitar um método setSaldo() porque ele permitiria alterar o saldo diretamente, sem verificar as regras da conta bancária. Isso poderia deixar o saldo com um valor inválido e comprometer a segurança do sistema. O correto é usar métodos como depositar() e sacar(), que verificam as regras antes de modificar o saldo.


5. **Modificadores de Acesso:** Qual é a diferença prática entre os modificadores `private` e `protected` quando subclasses entram em cena através do `extends`?
 R: O modificador `private` permite o acesso direto somente dentro da própria classe. Já o `protected` permite o acesso dentro da própria classe e também nas classes filhas. Por exemplo, uma classe `ContaCorrente` pode acessar diretamente um atributo `protected` herdado de `ContaBancaria`, mas não um atributo `private` dessa classe.

6. **Constructor Property Promotion:** Como o recurso de promoção de propriedades introduzido no PHP 8 reduz o *boilerplate* e melhora a legibilidade do código?

Resposta. O Constructor Property Promotion, introduzido no PHP 8, permite declarar e inicializar atributos diretamente nos parâmetros do construtor, utilizando modificadores como public, private ou protected. Isso reduz a quantidade de código repetido, chamada de boilerplate, tornando o código menor, mais organizado e fácil de entender.

7. **Propriedades `readonly`:** Explique o mecanismo de segurança proporcionado pela palavra-chave `readonly` em atributos de classes de valor como `Titular` ou `CPF`.
R. Uma propriedade `readonly` só pode ser inicializada uma única vez, normalmente no construtor. Depois disso, qualquer tentativa de alterá-la lança um erro. Isso garante imutabilidade: o CPF ou o nome do titular não mudam durante a vida do objeto, e ele nunca fica em estado inconsistente. Como é o próprio PHP que bloqueia a alteração, a propriedade pode ficar `public` com segurança, sem precisar de getter. O recurso exige PHP 8.1 ou superior.


8. **Classes Abstratas:** Por que faz todo sentido no modelo bancário que a classe `ContaBancaria` seja declarada como `abstract` enquanto `ContaCorrente` e `ContaPoupanca` são classes concretas?

R. Porque `ContaBancaria` representa um modelo geral de conta, com atributos e métodos comuns, mas não precisa ser instanciada diretamente. Já `ContaCorrente` e `ContaPoupanca` representam tipos específicos de contas e podem ser instanciadas, implementando suas próprias regras.

9. **Polimorfismo:** Como o método `transferir(float $valor, ContaBancaria $destino)` consegue operar transferências entre quaisquer tipos de contas filhas sem precisar saber qual é a classe exata da conta de destino?
R. Isso é possível porque ContaCorrente e ContaPoupanca herdam da classe ContaBancaria. Dessa forma, o método transferir() pode receber qualquer uma dessas contas pelo parâmetro $destino, sem precisar saber qual é o tipo exato. Isso facilita a reutilização do código e evita a necessidade de criar um método diferente para cada tipo de conta, desde que as regras necessárias para a transferência sejam respeitadas.

10. **Exceções de Domínio:** Por que é uma péssima prática retornar `false` ou `null` quando uma operação de saque é rejeitada por falta de saldo?
R. Retornar `false` ou `null` pode dificultar a identificação do motivo pelo qual o saque foi rejeitado. Utilizar uma exceção específica, como `SaldoInsuficienteException`, permite informar claramente o erro. Dessa forma, o código fica mais organizado e facilita o tratamento adequado dos erros financeiros.

---
