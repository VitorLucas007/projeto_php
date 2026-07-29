<?php

class validacoes
{
    /**
     * Valida CNPJ usando o algoritmo de dígitos verificadores (módulo 11).
     */
    public static function validar_cnpj($cnpj)
    {
        $cnpj = preg_replace('/[^0-9]/', '', (string) $cnpj);

        if (strlen($cnpj) != 14)
            return false;

        if (preg_match('/(\d)\1{13}/', $cnpj))
            return false;

        for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++) {
            $soma += $cnpj[$i] * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }
        $resto = $soma % 11;
        if ($cnpj[12] != ($resto < 2 ? 0 : 11 - $resto))
            return false;

        for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++) {
            $soma += $cnpj[$i] * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }
        $resto = $soma % 11;
        return $cnpj[13] == ($resto < 2 ? 0 : 11 - $resto);
    }

    /**
     * Formata CNPJ com máscara XX.XXX.XXX/XXXX-XX.
     */
    public static function formatar_cnpj($cnpj)
    {
        $cnpj = preg_replace('/[^0-9]/', '', (string) $cnpj);

        if (strlen($cnpj) != 14)
            return $cnpj;

        return substr($cnpj, 0, 2) . '.' .
               substr($cnpj, 2, 3) . '.' .
               substr($cnpj, 5, 3) . '/' .
               substr($cnpj, 8, 4) . '-' .
               substr($cnpj, 12, 2);
    }

    /**
     * Remove formatação do CNPJ, mantendo apenas dígitos.
     */
    public static function remover_formatacao_cnpj($cnpj)
    {
        return preg_replace('/[^0-9]/', '', (string) $cnpj);
    }

    /**
     * Valida CPF usando o algoritmo de dígitos verificadores (módulo 11).
     */
    public static function validar_cpf($cpf)
    {
        $cpf = preg_replace('/[^0-9]/', '', (string) $cpf);

        if (strlen($cpf) != 11)
            return false;

        if (preg_match('/(\d)\1{10}/', $cpf))
            return false;

        for ($i = 0, $j = 10, $soma = 0; $i < 9; $i++, $j--) {
            $soma += $cpf[$i] * $j;
        }
        $resto = ($soma * 10) % 11;
        $resto = ($resto == 10) ? 0 : $resto;
        if ($cpf[9] != $resto)
            return false;

        for ($i = 0, $j = 11, $soma = 0; $i < 10; $i++, $j--) {
            $soma += $cpf[$i] * $j;
        }
        $resto = ($soma * 10) % 11;
        $resto = ($resto == 10) ? 0 : $resto;
        return $cpf[10] == $resto;
    }

    /**
     * Formata CPF com máscara XXX.XXX.XXX-XX.
     */
    public static function formatar_cpf($cpf)
    {
        $cpf = preg_replace('/[^0-9]/', '', (string) $cpf);

        if (strlen($cpf) != 11)
            return $cpf;

        return substr($cpf, 0, 3) . '.' .
               substr($cpf, 3, 3) . '.' .
               substr($cpf, 6, 3) . '-' .
               substr($cpf, 9, 2);
    }

    /**
     * Remove formatação do CPF, mantendo apenas dígitos.
     */
    public static function remover_formatacao_cpf($cpf)
    {
        return preg_replace('/[^0-9]/', '', (string) $cpf);
    }
}
