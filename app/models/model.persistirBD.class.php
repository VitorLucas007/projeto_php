<?php

class persistirBD
{
    protected $db;
    protected $resultado;

    public function conectar()
    {
        $this->db = new mysqli(
            "127.0.0.1",
            "root",
            "",
            "projeto_pibeu"
        );

        if ($this->db->connect_error) {
            die("Erro ao conectar: " . $this->db->connect_error);
        }
    }

    public function desconectar()
    {
        if ($this->db) {
            $this->db->close();
        }
    }

    public function persistir($sql)
    {
        $this->resultado = $this->db->query($sql);

        if (!$this->resultado) {
            die("Erro SQL: " . $this->db->error);
        }
    }

    public function retornoConsultas()
    {
        $dados = [];

        if ($this->resultado instanceof mysqli_result) {
            while ($linha = $this->resultado->fetch_array(MYSQLI_NUM)) {
                $dados[] = $linha;
            }
        }

        return $dados;
    }
}