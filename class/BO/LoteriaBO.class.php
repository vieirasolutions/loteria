<?php
/**
 * Classe responsável pelas regras de negócio relacionadas à loteria
 * @author Breno Vieira Soares <brenovieirasoares1999@gmail.com>
 * @since 2019-09-20
 */
class LoteriaBO 
{
    private $quantidadeDezenas;
    private $resultado;
    private $totalJogos;
    private $jogos;
    private $dezenasPermitidas = [6,7,8,9,10];
    private $listaNumerosSorteio;
    private $quantidadeDezenasVitoria = 6;
    private $quantidadeNumerosParaSorteio = 60;
    
    /**
     * Método construtor que define a quantidade de dezenas o total de jogos
     * ao se criar um objeto de instância da classe LoteriaBO
     *
     * @param int $quantidadeDezenas
     * @param int $totalJogos
     */
    public function __construct(int $quantidadeDezenas, int $totalJogos)
    {
        $this->setQuantidadeDezenas($quantidadeDezenas)
            ->setTotalJogos($totalJogos)
            ->criaListaNumerosSorteio();
    }

    /**
     * Método responsável pelo sorteio dos números da loteria
     *
     * @return Array
     */
    private function escolherNumerosDaSorte(): Array
    {
        return $this->sortearDezenasAleatorias($this->getQuantidadeDezenas());
    }

    /**
     * Método responsável por criar os jogos
     *
     * @return void
     */
    public function jogar()
    {
        for ($i=0; $i < $this->getTotalJogos(); $i++)
        { 
            $this->setJogos($this->escolherNumerosDaSorte());
        }
    }

    /**
     * Método que realiza o sorteio dos numeros
     *
     * @return void
     */
    public function realizarSorteio()
    {
        $this->setResultado($this->sortearDezenasAleatorias($this->quantidadeDezenasVitoria));
    }

    /**
     * Método que trata os dados e retorna um array para exibicao dos resultados na tela
     *
     * @return Array
     */
    public function getDadosResultado(): Array
    {
        $arrayDadosExibicao = [];
        foreach ($this->getJogos() as $indiceJogo => $jogo)
        {
            $quantidadeDezenasSorteadas = $this->quantidadeDezenasSorteadas($jogo);
            $arrayDadosExibicao[] = [
                'quantidadeDezenasSorteadas' => $quantidadeDezenasSorteadas,
                'jogo'                       => $this->montaNumerosJogadosExibicaoTela($jogo),
                'ganhador'                   => $quantidadeDezenasSorteadas === $this->quantidadeDezenasVitoria
            ];
        }

        return $arrayDadosExibicao;
    }

    /**
     * Método para definir a quantidade de dezenas sorteadas
     *
     * @param array $jogo
     * @return int
     */
    private function quantidadeDezenasSorteadas($jogo)
    {
        // O array diff, retorna elementos do primeiro array que nao foram encontrados no segundo.
        // Sendo assim, eu pego o total de numeros jogados e subtraio na quantidade de numeros do jogo que não foram encontrados no array de resultado
        // Ou seja, o valor retornado é a quantidade de numeros sorteados
        return (count($jogo) - count(array_diff($jogo, $this->getResultado())));
    }

    /**
     * Método para definir a forma de exibição do jogo na tela
     *
     * @param array $jogo
     * @return String
     */
    private function montaNumerosJogadosExibicaoTela($jogo): String
    {
        $retorno = [];
        // Pega numeros que nao foram encontrados nos numeros sorteados
        $numerosNaoEncontrados = array_diff($jogo, $this->getResultado());
        foreach ($jogo as $key => $value)
        {
            // Verifica se o valor é um desses numeros não encontrados
            if (in_array($value, $numerosNaoEncontrados))
            {
                // Se sim, a posição recebe somente o valor
                $retorno[] = $value;
            }
            else
            {
                // Se não o valor fica em negrito (porque foi encontrado)
                $retorno[] = "<strong>$value</strong>";
            }
        }

        return implode(',',$retorno);
    }

    /**
     * Método genérico para sortear numeros aleátorio com base na quantidade de dezenas definida,
     * ou seja, ele pode ser usado tanto para sortear os numeros da loteria, quando para criar
     * o array de jogos aleatorio.
     *
     * @param int $quantidadeDezenas
     * @return Array
     */
    private function sortearDezenasAleatorias($quantidadeDezenas): Array
    {
        // Busca a lista de numeros que podem ser escolhidos
        $listaNumerosSorteio = $this->getListaNumerosSorteio();
        // Inicializa a variável que vai receber os numeros escolhidos
        $arrayNumerosEscolhidos = [];

        // A iteração limita a quantidade de numeros escolhidos com base na definição da variavel $quantidadeDezenas
        for ($i=0; $i < $quantidadeDezenas; $i++)
        {
            // Captura um índice de forma aleatória do array de numeros disponíveis a escolha
            $indiceEscolhido         = array_rand($listaNumerosSorteio);
            // Com o índice, define qual número foi escolhido, pegando o valor na posição do índice
            $numeroEscolhido         = $listaNumerosSorteio[$indiceEscolhido];
            // Faz uma atribuição do numero escolhido dentro do array de numeros escolhidos
            $arrayNumerosEscolhidos[] = $numeroEscolhido; 
            // Retira o número (remove a respectiva posição do número) que foi escolhido da lista dos próximos numeros aptos a serem escolhidos, evitando duplicidade
            unset($listaNumerosSorteio[$indiceEscolhido]);
        }

        // Ordena o array de numeros de forma ascendente
        sort($arrayNumerosEscolhidos);
        
        return $arrayNumerosEscolhidos;
    }

    /**
     * Cria lista de números aptos para o sorteio
     *
     * @return LoteriaBO
     */
    private function criaListaNumerosSorteio(): LoteriaBO
    {
        $listaNumerosSorteio = [];
        // Como definido, gera uma lista de 1 até a quantidade pré-definida
        for ($i=1; $i <= $this->quantidadeNumerosParaSorteio ; $i++) {
            $listaNumerosSorteio[] = $i;
        }
        $this->setListaNumerosSorteio($listaNumerosSorteio);

        return $this;
    }

    /**
     * Retorna o valor da variável quantidadeDezenas
     */ 
    public function getQuantidadeDezenas()
    {
        return $this->quantidadeDezenas;
    }

    /**
     * Define o valor da variável quantidadeDezenas
     *
     * @return  LoteriaBO
     */ 
    public function setQuantidadeDezenas($quantidadeDezenas): LoteriaBO
    {
        if (!in_array($quantidadeDezenas, $this->dezenasPermitidas))
        {
            throw new Exception("Dezena não permitida!", 1);
        }

        $this->quantidadeDezenas = $quantidadeDezenas;

        return $this;
    }

    /**
     * Retorna o valor da variável resultado
     */ 
    public function getResultado()
    {
        return $this->resultado;
    }

    /**
     * Define o valor da variável resultado
     *
     * @return  LoteriaBO
     */ 
    public function setResultado($resultado): LoteriaBO
    {
        $this->resultado = $resultado;

        return $this;
    }

    /**
     * Retorna o valor da variável totalJogos
     */ 
    public function getTotalJogos()
    {
        return $this->totalJogos;
    }

    /**
     * Define o valor da variável totalJogos
     *
     * @return  LoteriaBO
     */ 
    public function setTotalJogos($totalJogos): LoteriaBO
    {
        $this->totalJogos = $totalJogos;

        return $this;
    }

    /**
     * Retorna o valor da variável jogos
     */ 
    public function getJogos()
    {
        return $this->jogos;
    }

    /**
     * Define o valor da variável jogos
     *
     * @return  LoteriaBO
     */ 
    public function setJogos($jogo): LoteriaBO
    {
        $this->jogos[] = $jogo;

        return $this;
    }

    /**
     * Retorna o valor da variável listaNumerosSorteio
     */ 
    public function getListaNumerosSorteio(): Array
    {
        return $this->listaNumerosSorteio;
    }

    /**
     * Define o valor da variável listaNumerosSorteio
     *
     * @return  LoteriaBO
     */ 
    public function setListaNumerosSorteio($listaNumerosSorteio): LoteriaBO
    {
        $this->listaNumerosSorteio = $listaNumerosSorteio;

        return $this;
    }
}
