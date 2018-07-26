//svcAxios: serviço responsável por realizar os post requests
function svcAxios(){
    return{
        getAxios: getAxios,
    };

    function getAxios(url, controller, action, parameters){
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
    
}

angular.module('app').factory('svcAxios', svcAxios);