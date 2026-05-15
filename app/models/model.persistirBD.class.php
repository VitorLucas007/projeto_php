<?php

class persistirBD
{

    protected $servidor;
    protected $usuario;
    protected $senha;
    protected $banco;
    protected $consulta;
    protected $resultado;
    protected $db;

    function __construct($srv, $usu, $pass, $bd)
    {
        $this->servidor = $srv;
        $this->usuario = $usu;
        $this->senha = $pass;
        $this->banco = $bd;
    }

    public function conectar()
    {
        $this->db = new mysqli($this->servidor, $this->usuario, $this->senha, $this->banco);

        if (mysqli_connect_errno()) {
            return 0;
        } else {
            return 1;
        }
    }

    public function desconectar()
    {
        $this->db->close();
    }

    public function persistir($sql)
    {
        $this->consulta = $sql;
        $this->resultado = $this->db->query($this->consulta);
    }

    public function retornoConsultas()
    {
        while ($ttuplas = $this->resultado->fetch_array(MYSQLI_NUM)) {
            $tuplas[] = $ttuplas;
        }

        return $tuplas;
    }
}
