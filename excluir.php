<?php

require __DIR__ . '/vendor/autoload.php';

use \App\Entity\Vaga;

// Validação do ID
if(!isset($_GET['id']) or !is_numeric($_GET['id'])):
    header('location: index.php?status=error');
    exit;
endif;

// consulta a vaga
$obVaga = Vaga::getVaga($_GET['id']);

// Validação da vaga
if(!$obVaga instanceof Vaga):
    header('location: index.php?status=error');
    exit;
endif;

// validação do post
if(isset($_POST['excluir'])):
    $obVaga->excluir();
    
    header('location: index.php?status=success');
    exit;
endif;

include  __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/confirmar-exclusao.php';
include __DIR__ . '/includes/footer.php';