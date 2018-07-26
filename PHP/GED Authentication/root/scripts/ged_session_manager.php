<?php
    include_once("credential.php"); //incluindo credential para uso na classe
    
    /*

     * GedSessionManager: classe responsável por fazer a autorização do usuário com o servidor, 
     * guardando erros, retornos de requests e as credenciais.
     * 
     */
    class GedSessionManager
    {
        //Variáveis utilizadas para futuras requests
        private $url;
        private $credential;

        //Variáveis auxiliares guardando status da sessão
        private $error;
        private $returnJson;
        private $httpRedirect;

        //Construtor, todo objeto gedSessionManager precisa de informações básicas para utilizar futuramente em um request
        public function __construct($url, $username, $password) 
        {    
            $this->url = $url;
            $this->credential = new Credential($username, $password);
        }

        //retorna credentials para autentificação no servidor
        public function GetCredential()
        {
            return $this->credential;
        }

        //retorna url
        public function GetUrl()
        {
            return $this->url;
        }

        //retorna json com status da autorização
        public function GetJson()
        {
            return $this->returnJson;
        }

        //retorna possíveis erros
        public function GetError()
        {
            return $this->error;
        }

        //seta possíveis erros
        public function SetError($error) 
        {
            $this->error = $error;
        }
        
        //seta json com status da autorização
        public function SetJson ($json) 
        {
            $this->returnJson = $json;
        }

        //seta url para redirecionamento no ged
        public function SetHTTPRedirect($httpRedirect) 
        {
            $this->httpRedirect = $httpRedirect;
        }

        //retorna url para redirecionamento no ged.
        public function GetHTTPRedirect() 
        {
            return $this->httpRedirect;
        }
    }