<?php

namespace App\Db;

use \PDO;
use \PDOException;

class Database {
    
    /**
     * Host de conexão com o banco de dados
     * @var string
     */
    const HOST = 'localhost';
    
    /**
     * Nome do banco de dados
     * @var string
     */
    const NAME = "wdev_vagas";
    
    /**
     * Usuário do banco de dados
     * @var string
     */
    const USER = "root";
    
    /**
     * Senha de acesso ao banco de dados
     * @var string
     */
    const PASS = "";
    
    /**
     * Nome da tabela a ser manipulada
     * @var string 
     */
    private $table;
    
    /**
     * Instância de conexão com o banco de dados
     * @var PDO
     */
    private $connection;
    
    /**
     * Define a tabela e instância e conexão
     * @param string $table
     */
    public function __construct($table = null) {
        $this->table = $table;
        $this->setConnection();
    }
    
    /**
     * Método responsável por criar uma conexão com o banco de dados
     */
    private function setConnection() {
        try {
            $this->connection = new \PDO('mysql:host=' . self::HOST . ';dbname=' . self::NAME, self::USER, self::PASS);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $ex) {
            // não expor um error do banco de dados para o usuário final
            // certo a se fazer é: gravar o erro do banco no log e emitir um erro amigável para o usuário final
            die('ERROR: ' . $ex->getMessage());
        }
    }
    
    /**
     * Método responsável por executar queries dentro do banco de dados
     * @param string $query
     * @param array $params
     * @return PDOStatement
     */
    public function execute($query, $params = []) {
        try {
            $statement = $this->connection->prepare($query);
            $statement->execute($params);
            return $statement;
        } catch (PDOException $ex) {
            die('ERROR: ' . $ex->getMessage());
        }
    }
    
    /**
     * Método responsável por inserir dados no banco
     * @param array [ field => value ]
     * @return integer ID inserido
     */
    public function insert($values) {
        // dados da query
        $fields = array_keys($values);
        $binds = array_pad([], count($fields), '?');
        
        // monta a query
        $query = 'INSERT INTO ' . $this->table . ' (' . implode(',', $fields) . ') VALUES (' . implode(',', $binds) . ')';
        
        // executa o insert
        $this->execute($query, array_values($values));
        
        // retornar o id inserido
        return $this->connection->lastInsertId();
    }
    
    /**
     * Método responsável por executar uma consulta no banco de dados
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $fields 
     * @return PDOStatement
     */
    public function select($where = null, $order = null, $limit = null, $fields = '*') {
        // dados da query
        $where = strlen($where) ? 'WHERE ' . $where: '';
        $order = strlen($order) ? 'ORDER BY ' . $order: '';
        $limit = strlen($limit) ? 'LIMIT ' . $limit: '';
        
        // monta a query
        $query = 'SELECT' . $fields . 'FROM ' . $this->table . ' ' . $where . ' ' . $order . ' ' . $limit;
        
        // executa a query
        return $this->execute($query);
    }
    
    /**
     * Método responsável por executar atualizações no banco de dados
     * @param string $where
     * @param array $values [ field => value ]
     */
    public function update($where, $values) {
        // dados da query
        $fields = array_keys($values);
        
        // monta query
        $query = 'UPDATE ' . $this->table .  ' SET ' . implode('=?, ', $fields) . '=? WHERE ' . $where;
        
        // executar a query
        $this->execute($query, array_values($values));
        
        // retorna sucesso
        return true;
    }
    
    /**
     * Método responsável por excluir dados do banco de dados
     * @param string $where
     * @return boolean
     */
    public function delete($where) {
        // monta a query
        $query = 'DELETE FROM ' . $this->table . ' WHERE ' . $where;
        
        // executa a query
        $this->execute($query);
        
        // retorna sucesso
        return true;
    }
}
