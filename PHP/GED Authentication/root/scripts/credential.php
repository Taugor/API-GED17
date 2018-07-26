<?php
    /*
     * Credential: classe que será encodada para json para realizar autorização nas requests
     * 
    */
    class Credential 
    {
        //informações do usuário 
        //necessário possuir esses nomes de váriavels pois é o que o servidor espera para realizar Handshake e Autorização.
        public $userName;
        public $password;

        //atribuição na construção de username e password
        public function __construct ($username, $password) 
        {
            $this->userName = $username;
            $this->password = $password;
        }
    }