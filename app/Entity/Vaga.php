<?php

namespace App\Entity;

use \App\Db\Database;
use \PDO;
 
class Vaga {
    /**
     * Identificador único da vaga
     * @var integer
     */
    public $id;
    
    /**
     * Título da vaga
     * @var string 
     */
    public $titulo;
    
    /**
     * Descrição da vaga (pode conter html)
     * @var string 
     */
    public $descricao;
    
    /**
     * Define se a vaga ativa
     * @var string (s/n) 
     */
    public $ativo;
    
    /**
     * Data de publicação da vaga
     * @var string
     */
    public $data;
    
    /**
     * Método responsável por cadastrar uma nova vaga no banco
     * @return boolean
     */
    public function cadastrar() {
        // Definir a data
        $this->data = date('Y-m-d H:i:s');
        
        // Inserir a vaga no banco
        $obDatabase = new Database('vagas');
        $this->id = $obDatabase->insert([
            'titulo' => $this->titulo,
            'descricao' => $this->descricao,
            'ativo' => $this->ativo,
            'data' => $this->data
        ]);
                
        // Retornar sucesso
        return true;
    }
    
    /**
     * Método responsável por atualizar a vaga no banco
     * @return boolean
     */
    public function atualizar() {
        return (new Database('vagas'))->update('id = ' . $this->id, [
            'titulo' => $this->titulo,
            'descricao' => $this->descricao,
            'ativo' => $this->ativo,
            'data' => $this->data
        ]);
    }
    
    /**
     * Método responsável por excluir a vaga do banco de dados
     * @return boolean
     */
    public function excluir() {
        return (new Database('vagas'))->delete('id = ' . $this->id);
    }
    
    /**
     * Método responsável por obter as vagas do banco de dados
     * @param string $where
     * @param string $order
     * @param string $limit
     * @return array
     */
    public static function getVagas($where = null, $order = null, $limit = null) {
        return (new Database('vagas'))->select($where, $order, $limit)
                                      ->fetchAll(PDO::FETCH_CLASS, self::class);
    }
    
    /**
     * Método responsável por buscar uma vaga com base em seu ID
     * @param integer $id
     * @return Vaga
     */
    public static function getVaga($id) {
        return (new Database('vagas'))->select('id = ' . $id)
                                      ->fetchObject(self::class);
    }
}