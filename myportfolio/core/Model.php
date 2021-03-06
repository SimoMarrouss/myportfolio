<?php

class Model
{

    static $connections = array();
    public $conf = 'default';
    public $table = false;
    public $db;

    public function __construct()
    {
        $conf = Conf::$databases[$this->conf];
        if (isset(Model::$connections[$this->conf])) {
            $this->db = Model::$connections[$this->conf];
            return true;
        }
        try {
            $pdo = new PDO('mysql:host=' . $conf['host'] . ';dbname=' . $conf['database'] . ';', $conf['login']
                , $conf['password']);
            Model::$connections[$this->conf] = $pdo;
            $this->db = $pdo;
        } catch (PDOException $e) {
            die(print_r($e, true));
        }

        if ($this->table === false) {
            $this->table = strtolower(get_class($this)) . 's';
        }
    }

    public function find($req)
    {
        $sql = 'SELECT * FROM ' . $this->table . ' as ' . get_class($this) . '';
        if (isset($req['conditions'])) {
            $sql .= ' WHERE ' . $req['conditions'];
        }
        $pre = $this->db->prepare($sql);
        $pre->execute();
        return $pre->fetchAll(PDO::FETCH_OBJ);
    }

    public function findFirst($req)
    {
        return current($this->find($req));
    }
}

?>