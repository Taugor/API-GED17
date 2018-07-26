const SuperHeroLookalikes = {
    redtornado: 'vision',
    deathstroke: 'deadpool',
    bigbarda: 'gamora',
    redhood: 'wintersoldier',
    swampthing: 'manthing',
    greenarrow: 'hawkeye',
    aquaman: 'namor',
    elongatedman: 'mrfantastic',
    atom: 'antman',
    doompatrol: 'xmen',
    catwoman: 'blackcat',
    greenlantern: 'nova',
    batman: 'moonknight',
    wonderwoman: 'powerprincess',
    superman: 'hyperion'
};


function getAxios(url, controller, action, parameters) {
    return axios({
        method: 'POST',
        url: url + '/_layouts/TaugorGED17/Services/MainController.aspx/ExecuteAction',
        headers: {
            'Content-Type': 'application/json; charset=utf-8',
        },
        data: JSON.stringify({
            controller: controller,
            action: action,
            parameters: parameters || null,
            username: null,
            requestToken: null,
        }),
    });
}


var gedAuthentication = function ($scope, svcAxios, svcAuthentication) {

    var vm = this;
    vm.dadosGedCredencials = {};
    vm.logar = logar;
    vm.logarNoGed = logarNoGed;

    function logar() {

        vm.dadosGedCredencials = angular.copy(vm.dadosGedCredencialUnity);

        //#region -- crendeciais -- 
        /*Este é são as credenciais que você utilizará sempre para testar no contexto que deseja.
        Caso queira realizar login de algum usuário e buscar os documentos dele somente, a API vai trazer somente o que ele tem acesso.
        Caso queira realizar ações como ADMIN, dev realizar o login como ADM para outra operações*/
        //#endregion

        const credentialsUserName = {
            userName: vm.dadosGedCredencials.login,
            password: vm.dadosGedCredencials.senha
        };


        vm.logarNoGed(vm.dadosGedCredencials.URL, credentialsUserName, vm.dadosGedCredencials);
    }

    function logarNoGed(gedUrl, credentials, dadosGedCredencial) {
        if (gedUrl.indexOf('http://')) {
            return (dadosGedCredencial.error = "Erro na autenticação: favor informar url válida e tente novamente");
        }

        svcAxios.getAxios(gedUrl, 'Authentication', 'Handshake', {
            credentials,
            subject: 'SmartECM'
        }).then(
            handshakeResult => {
                const question = handshakeResult.data.d;
                svcAxios.getAxios(gedUrl, 'Authentication', 'Authenticate', {
                    credentials,
                    subject: 'SmartECM',
                    fingerprint: svcAuthentication.SuperHeroLookalikes[question]
                }).then(authenticationResult => {
                    try {
                        dadosGedCredencial.error = null;
                        const result = authenticationResult.data.d;
                        dadosGedCredencial.returnJson = authenticationResult.data; //coloca os dados retornados
                        const loginName = result.User.LoginName.substring(1 + result.User.LoginName.indexOf("\\"), result.User.LoginName.length);
                        const url = '${gedUrl}/_layouts/TaugorGED17/App/Authenticate.aspx?username=${loginName}&token=${result.RequestToken}';
                        dadosGedCredencial.httpRedirect = url;
                    } catch (err) {
                        dadosGedCredencial.error = "Erro na autenticação:" + err.message;
                    }

                    $scope.$digest();
                    return dadosGedCredencial;

                }, err => {
                    dadosGedCredencial.error = err;
                    $scope.$digest();
                });
            }, err => {
                dadosGedCredencial.error = err;
                $scope.$digest();
            }
        );
    }
};


