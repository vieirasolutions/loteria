<?php

require_once 'class/BO/LoteriaBO.class.php';
require_once 'vendor/autoload.php';

$quantidadeDezenas = 10;
$totalJogos = 3;

$error = new stdClass();
$error->ocorreuErro = false;

try
{
    $loteriaBO = new LoteriaBO($quantidadeDezenas, $totalJogos);
    $loteriaBO->jogar();
    $loteriaBO->realizarSorteio();
}
catch (\Throwable $th)
{
    $error->ocorreuErro = true;
    $error->mensagem = $th->getMessage();
}
finally
{
    $loader = new \Twig\Loader\FilesystemLoader('templates');
    $twig = new \Twig\Environment($loader);

    $dadosTemplate = [];

    $dadosTemplate['erro'] = $error;

    if (!$error->ocorreuErro)
    {
        $dadosTemplate['dadosListagem'] = $loteriaBO->getDadosResultado();
        $dadosTemplate['resultado'] = $loteriaBO->getResultado();
    }

    echo $twig->render('loteria.twig', $dadosTemplate);
}
