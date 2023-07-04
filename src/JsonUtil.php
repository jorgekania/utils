<?php

namespace Project\Utils;

/**
 * Class MathHelper
 *
 * This class provides helper methods for handling json.
 *
 * @package Project\Utils
 */
class JsonUtil
{
    /**
     * Validate whether or not a string is valid json
     *
     * @param string $string
     */
    public static function jsonValidate($string)
    {
        // decodificar os dados JSON
        $result = json_decode($string);

        match (json_last_error()) {
            JSON_ERROR_NONE => $error = '', // JSON é válido // Nenhum erro ocorre,
            JSON_ERROR_DEPTH            => $error = 'A profundidade máxima da pilha foi excedida.',
            JSON_ERROR_STATE_MISMATCH   => $error = 'JSON inválido ou mal formado.',
            JSON_ERROR_CTRL_CHAR        => $error = 'Erro de caractere de controle, possivelmente codificado incorretamente.',
            JSON_ERROR_SYNTAX           => $error = 'Erro de sintaxe, JSON malformado.',
            JSON_ERROR_UTF8             => $error = 'Caracteres UTF-8 malformados, possivelmente codificados incorretamente.',
            JSON_ERROR_RECURSION        => $error = 'Uma ou mais referências recursivas no valor a ser codificado.',
            JSON_ERROR_INF_OR_NAN       => $error = 'Um ou mais valores NAN ou INF no valor a ser codificado.',
            JSON_ERROR_UNSUPPORTED_TYPE => $error = 'Foi fornecido um valor de um tipo que não pode ser codificado.',
            default                     => $error = 'Ocorreu um erro JSON desconhecido.'
        };

        if ($error !== '') {
            return [
                'return'  => false,
                'message' => $error,
            ];
        }

        return [
            'return'  => true,
            'message' => $result,
        ];
    }

    /**
     * Returns the value within a json key
     *
     * @param string $string
     * @param string $key
     * @param string $parentKey
     */
    public static function searchValueInJson(string $json, string $key, ?string $parentKey = null)
    {
        // Verifica se o json enviado é valido
        $verify = self::jsonValidate($json);

        if (!$verify['return']) {
            return $verify['message'];
        }

        // Converte o JSON em um array multidimensional
        $jsonArray = json_decode($json, true);
        // Verifica se a chave pai foi especificada
        if (!$parentKey) {
            // Busca a chave desejada diretamente no array multidimensional
            $result = self::searchKeyInArray($key, $jsonArray);
        } else {
            // Percorre o array multidimensional até encontrar a chave pai desejada
            $parentArray = self::searchKeyInArray($parentKey, $jsonArray);
            // Busca a chave desejada dentro da chave pai
            $result = self::searchKeyInArray($key, $parentArray);
        }
        return $result;
    }

    private static function searchKeyInArray($key, $array)
    {
        $result = [];
        // Percorre o array em busca da chave desejada
        foreach ($array as $k => $value) {
            if ($k === $key) {
                // Encontrou a chave desejada
                return $value;
            } elseif (is_array($value)) {
                // Verifica se a chave desejada está dentro deste array
                $result = self::searchKeyInArray($key, $value);
                if ($result) {
                    return $result;
                }
            }
        }

        // Não encontrou a chave desejada
        return null;
    }
}
