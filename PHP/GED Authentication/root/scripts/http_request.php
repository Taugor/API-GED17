<?php

    /* 
     *  HTTPRequest: classe que será reponsável por realizar todos os tipos de http request.
     */
    class HTTPRequest 
    {
        /*
         * PostRequest: realiza um post request na $url espeficiada, postando parâmetros ($data) para retornar um json do servidor.
         * utilizando a biblioteca nativa do php, Client URL (cURL).
         */

        public static function PostRequest($url, $data)
        {
            
            $ch = curl_init($url); //abrindo a request e definindo um handler da cURL.

            $encodedData = json_encode($data); //converte os parâmetros para json

            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST"); //define que o método utilizado será POST
            curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData); //envia a data convertida para o servidor 
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8')); //define que o tipo da data e o encode

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //permite que aja um returno do servidor (no caso um json)

            $result = curl_exec($ch); //executa o post request
            
            //tratamento de erro
            if (curl_errno($ch)) {  
                print curl_error($ch); 
            } 
            
            curl_close($ch); //fechando a request

            return json_decode($result); //retornando o json convertido para php para utilizações na aplicação.
        }
    }