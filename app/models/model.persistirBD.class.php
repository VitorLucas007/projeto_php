<?php

class persistirBD
{
    // Credenciais centralizadas aqui (ideal seria em .env fora do webroot,
    // mas para o escopo do projeto isso já elimina a duplicação em cada model)
    private const DB_HOST = "127.0.0.1";
    private const DB_USER = "root";
    private const DB_PASS = "";
    private const DB_NAME = "projeto_pibeu";

    protected $db;
    protected $resultado;
    private $ultimoIdInserido = 0;

    public function conectar()
    {
        $this->db = new mysqli(
            self::DB_HOST,
            self::DB_USER,
            self::DB_PASS,
            self::DB_NAME
        );

        if ($this->db->connect_error) {
            die("Erro ao conectar ao banco de dados.");
        }

        $this->db->set_charset('utf8mb4');
    }

    public function desconectar()
    {
        if ($this->db) {
            $this->db->close();
        }
    }

    /**
     * Mantido apenas para SELECTs simples sem parâmetros vindos do usuário
     * (ex: "SELECT * FROM pessoa ORDER BY nome").
     * NUNCA usar para concatenar entrada de formulário/GET/POST.
     */
    public function persistir($sql)
    {
        $this->resultado = $this->db->query($sql);

        if (!$this->resultado) {
            error_log("Erro SQL: " . $this->db->error);
            throw new \RuntimeException("Erro ao processar sua solicitação.");
        }
    }

    /**
     * Prepared statement. $tipos segue o padrão do mysqli (ex: "ssi" para
     * duas strings e um inteiro). $valores é um array na mesma ordem dos "?".
     */
    public function persistirPreparado($sql, $tipos, $valores)
    {
        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            error_log("Erro ao preparar SQL: " . $this->db->error);
            throw new \RuntimeException("Erro ao processar sua solicitação.");
        }

        if (!empty($valores)) {
            $stmt->bind_param($tipos, ...$valores);
        }

        if (!$stmt->execute()) {
            error_log("Erro ao executar SQL: " . $stmt->error);
            $stmt->close();
            throw new \RuntimeException("Erro ao processar sua solicitação.");
        }

        $this->resultado = $stmt->get_result();
        $this->ultimoIdInserido = $this->db->insert_id;

        $stmt->close();
    }

    public function ultimoId()
    {
        return $this->ultimoIdInserido;
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

    public function iniciarTransacao()
    {
        $this->db->begin_transaction();
    }

    public function confirmarTransacao()
    {
        $this->db->commit();
    }

    public function desfazerTransacao()
    {
        $this->db->rollback();
    }
}
