(function () {
    "use strict";

    /* 
     * gedAuthentication: função do controller do component gedAuthentication. 
     * svcAxios: serviço responsável por realizar os post requests
     * svcAuthentication: serviço que possuí variáveis para auxiliar na autorização
     */
    var gedAuthentication = function (svcAxios, svcAuthentication) {

        var vm = this;               //variável de controle definida no controllerAs do component gedAuthentication;

        vm.credentials = {};         //credencial utilizada para realizar autorização
        vm.gedSessionManager = {};   //objeto que cuidará da autorização

        vm.tryToLogin = tryToLogin;  //função que prepara e chama tryToLoginOnGED para logar no servidor
        vm.tryToLoginOnGED = tryToLoginOnGED;  //função responsável por logar no servidor 

         //função que prepara e chama tryToLoginOnGED para logar no servidor
        function tryToLogin() {

            //#region -- crendeciais -- 
            /*Este é são as credenciais que você utilizará sempre para testar no contexto que deseja.
            Caso queira realizar login de algum usuário e buscar os documentos dele somente, a API vai trazer somente o que ele tem acesso.
            Caso queira realizar ações como ADMIN, dev realizar o login como ADM para outra operações*/
            //#endregion
            vm.credentials = {
                userName: vm.gedSessionManager.username,
                password: vm.gedSessionManager.password
            };

            //chamando função responsável por logar no servidor
            vm.tryToLoginOnGED(vm.gedSessionManager.URL, vm.credentials, vm.gedSessionManager);
        }

        //função responsável por logar no servidor 
        function tryToLoginOnGED(url, credentials, gedSessionManager) {

            if (url.indexOf('http://')) {
                return (gedSessionManager.error = "Erro na autenticação: favor informar url válida e tente novamente");
            }

            //parâmetros do request de handshake
            var authorizationParameters = {
                credentials,          //credenciais do usuário
                subject: 'SmartECM'   //loggando no SmartECM
            };

            //Efetuando handshake apartir de um post request
            svcAxios.getAxios(url, 'Authentication', 'Handshake', authorizationParameters).then(handshakeResult => {
                
                //utilizando da resposta do handshake para criar um fingerprint para autorização.
                var question = handshakeResult.data.d;

                //parâmetros do request de autorização
                authorizationParameters = {
                    credentials,//credenciais do usuário
                    subject: 'SmartECM',
                    fingerprint: svcAuthentication.SuperHeroLookalikes[question] //colocando fingerprint nos parâmetros para um post request de autenticação
                };

                //efetuando request de autorização
                svcAxios.getAxios(url, 'Authentication', 'Authenticate', authorizationParameters)
                    .then(authenticationResult => { //se a resposta do post request for válida
                        try {
                            gedSessionManager.error = null;                           //deu certo! setando que não houve erro
                            var result = authenticationResult.data.d;                 //armazenando json resultado do post request
                            gedSessionManager.returnJson = authenticationResult.data; //setando json retornado pelo servidor no session manager

                            //montado url para redirect
                            var login = result.User.LoginName.substring(1 + result.User.LoginName.indexOf("\\"), result.User.LoginName.length);
                            var redirectUrl = url + '/_layouts/TaugorGED17/App/Authenticate.aspx?username=' + login + '&token=' + result.RequestToken;
                            
                            //setando url para redirect
                            gedSessionManager.httpRedirect = redirectUrl;
                        } catch (err) {
                            //setando erro na autenticação no session manager
                            gedSessionManager.error = "Erro na autenticação:" + err.message;
                        }

                        //atualizar a tela pois mudou externamente ao angular a variável vm.gedSessionManager
                        $scope.$digest();
                        //retorna o manager da sessão
                        return gedSessionManager; 

                    }, err => {
                        //se retorno da resquest de autorização for null
                        gedSessionManager.error = err; 
                        $scope.$digest(); //atualizar a tela pois mudou externamente ao angular a variável vm.gedSessionManager
                    });
            }, err => {
                //se retorno da resquest de handshake for null
                gedSessionManager.error = err;
                $scope.$digest(); //atualizar a tela pois mudou externamente ao angular a variável vm.gedSessionManager
            });
        }
    };

    //Controller
    angular.module('app').controller('gedAuthentication', gedAuthentication);

    //Component
    angular.module('app').component('gedAuthentication', {
        templateUrl: '../../app/public/authentication.html',
        controller: 'gedAuthentication',
        controllerAs: 'vm'
    });
})();
