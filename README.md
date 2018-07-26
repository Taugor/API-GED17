# API-GED17
Exemplos de códigos para integração com sistema de gestão eletrônica de documentos da Taugor

<h2>HTTP Requests</h2>

content-type	application/json; charset=UTF=8
method	POST
url	{url}/layouts/TaugorGED17/Services/MainController.aspx/ExecuteAction
body/data/payload	{
"controller":"Authentication",
"action":"{action : string}",
"parameters”: {parameters : object},
"username":null,
"requestToken":null
}

WHERE
url = Taugor GED’s URL
action = action to execute, according to the steps below
parameters = parameters for the given action, according to the steps below


Step 1 – Handshake
Returns
question: string
Arguments:
action = "Handshake"
parameters =  {
"credentials":{
"UserName": {username: string},
"Password": {password: string}
},
"subject": {subject: string}
}

WHERE
username = user’s account
password = user account’s password
subject = eg.: "SSO"

 
Step 2 – Authenticate
Returns
{
"User": { 
LoginName: string,
[…]
}
"RequestToken": string
}

Arguments:
action = "Authenticate"
parameters =  {
"credentials":{
"UserName": {username: string},
"Password": {password: string}
},
"subject": {string}
"fingerprint": {fingerprint: string}
}

WHERE
username = user’s account (same as in Step 1)
password = user account’s password (same as in Step 1)
subject = as given by Taugor (same as in Step 1)	
fingerprint = answer to the handshake’s question as instructed by Taugor


Códido de Exemplo de utilização 
 

//Here's a simple JavaScript implementation for abstracting the http request using axios
/*
 function http(url, controller, action, parameters){
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
*/

//Dummy validation for the presentation
const SuperHeroLookalikes = { 
    redtornado: 'vision'
} 

//url for your Taugor GED environment
//(this is a development environment without DNS)
const gedUrl = 'http://186.223.228.136:5003'

//These credentials should be for the user which you want to authenticate
const credentials = { userName: 'Administrator', password: 'Absirwt600' }

   
//The following method used, API.actionCamelCase is an abstraction for the Http Request like the one above,
//with an additional step for getting only the data and transforming the fields to camel case hence the server returns in pascal case

//Start with a handshake and get the question
API.actionCamelCase('Authentication', 'Handshake', {
    credentials,
    subject: 'SmartECM'
 }).then(question => {
     //As you receive the question, answer it to complete your authentication
     API.actionCamelCase('Authentication', 'Authenticate', {
	     credentials,
	     subject: 'SmartECM',
	     fingerprint: SuperHeroLookalikes[question]
     }).then(r => {
             //Finally, send the use to the following URL
	     const url = `${gedUrl}/_layouts/TaugorGED17/App/Authenticate.aspx?username=${r.user.loginName}&token=${r.requestToken}`
             window.location.href = url
     })
})
