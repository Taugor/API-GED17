<html>
    <body>
        <!-- Realização da Autorização -->
        <?php
            include_once("./scripts/credential.php");          //incluindo classe que cuidará dos dados do usuário
            include_once("./scripts/ged_session_manager.php"); //incluindo classe que cuidará da autorização
            include_once("./scripts/http_request.php");        //incluindo classe para realizar post requests

            //objeto que cuidará da autorização
            $gedSessionManager = tryToLogin($_POST["url"], $_POST["username"], $_POST["password"]);
            
            /* tryToLogin: função responsável por logar no servidor */
            function tryToLogin($url, $username, $password)
            {
                //objeto que cuidará da autorização
                $gedSessionManager = new GedSessionManager($url, $username, $password);
                
                //credencial utilizada para realizar autorização
                $gedCredentials = $gedSessionManager->GetCredential();

                //erro caso url não inicie com http e seja inválida
                if(strpos($url, "http://")){
                    return $gedSessionManager->SetError("Erro na autenticação: favor informar url válida e tente novamente");
                }
                
                //parâmetros do request de handshake
                $authorizationParameters = array(
                    'credentials' => $gedCredentials, //credenciais do usuário
                    'subject' => 'SmartECM'           //loggando no SmartECM
                );

                //Efetuando handshake apartir de um post request e guardando resposta do resultado.
                $handshakeResponse = postRequest($url, 'Authentication', 'Handshake', $authorizationParameters);
            
                //se a resposta do post request for válida
                if($handshakeResponse->d != null){

                    //array auxiliar para realizar autorização
                    $superHeroLookalikes = array(
                        'redtornado' => 'vision',
                        'deathstroke' => 'deadpool',
                        'bigbarda' => 'gamora',
                        'redhood' => 'wintersoldier',
                        'swampthing' => 'manthing',
                        'greenarrow' => 'hawkeye',
                        'aquaman' => 'namor',
                        'elongatedman' => 'mrfantastic',
                        'atom' => 'antman',
                        'doompatrol' => 'xmen',
                        'catwoman' => 'blackcat',
                        'greenlantern' => 'nova',
                        'batman' => 'moonknight',
                        'wonderwoman' => 'powerprincess',
                        'superman' => 'hyperion'
                    );
                
                    //utilizando da resposta do handshake para criar um fingerprint para autorização.
                    $question = $handshakeResponse->d;
                    $fingerprint = $superHeroLookalikes[$question];

                    //parâmetros do request de autorização
                    $authorizationParameters = array(
                        'credentials' => $gedCredentials, //credenciais do usuário
                        'subject' => 'SmartECM',
                        'fingerprint' => $fingerprint //colocando $fingerprint nos parâmetros para um post request de autenticação
                    );


                    //efetuando request de autorização e guardando resultado
                    $authenticationResult = postRequest($url, 'Authentication', 'Authenticate', $authorizationParameters);

                    if($authenticationResult != null){

                        try{
                            $gedSessionManager->SetError(null);   //deu certo! setando que não houve erro
                            $result = $authenticationResult->d;   //armazenando json resultado do post request
                            $gedSessionManager->SetJson($result); //setando json retornado pelo servidor no session manager

                            //montado url para redirect
                            $loginName = $result->User->LoginName = substr(1 + strrpos($result->User->LoginName, "\\"), strlen($result->User->LoginName));
                            $redirectUrl = $url.'/_layouts/TaugorGED17/App/Authenticate.aspx?username='.$loginName.'&token='.$result->RequestToken;

                            //setando url para redirect
                            $gedSessionManager->SetHTTPRedirect($redirectUrl);
                        } catch (Exception $error){
                            //setando erro na autenticação no session manager
                            $gedSessionManager->SetError("Erro na autenticação:" + $error->getMessage());
                        }

                        //retorna o manager da sessão
                        return $gedSessionManager; 
                        
                    } else {
                        //se retorno da resquest de autorização for null
                        $gedSessionManager->SetError("Authorization request retornou null ");
                        consoleLog($gedSessionManager->GetError());
                        return $gedSessionManager; 
                    }
                } else{
                    //se retorno da resquest de handshake for null
                    $gedSessionManager->SetError("Handshake request retornou null ");
                    consoleLog($gedSessionManager->GetError());
                    return $gedSessionManager; 
                }
            }

            //postRequest: responsável para criar estrutura de dados que o servidor espera e realizar/retornar post request das autorizações.
            function postRequest($url, $controller, $action, $parameters)
            {
                $requestUrl = $url.'/_layouts/TaugorGED17/Services/MainController.aspx/ExecuteAction';
                //estrutura que o servidor espera
                $data = array(
                    'controller' => $controller,
                    'action' => $action,
                    'parameters' => $parameters,
                    'username'=> null,
                    'requestToken'=> null
                );

                //retornando json via post request
                return (HTTPRequest::PostRequest($requestUrl, $data));
            }

            //Função auxiliar para debbugar no console alguma váriavel php
            function consoleLog($data)
            {
                //hibrido com javascript, convertendo váriavel php para json para mostrar no console
                echo "<script>console.log(".json_encode($data).");</script>";
            }
        ?>

        <!--  Continuação do HTML  -->
        <div class="container my-5">
            <form> 
                <!-- se não temos erros no manager da sessão mostrar:  -->
                <?php if ($gedSessionManager != null && $gedSessionManager->GetError() == null) { ?> 
                    <div class="form-group mt-5">
                        <?php if ($gedSessionManager->GetHTTPRedirect() != null) {?> 
                            <div class="form-group">
                                <h1>Retorno do Login</h1>
                                <div class="well">
                                    <h2>JSON Completo</h2>
                                    <label>
                                        <span style="white-space: normal;">
                                            <?php 
                                                echo (json_encode($gedSessionManager->GetJson()));
                                            ?>
                                        </span>
                                    </label>
                                </div>

                                <div class="well">
                                    <h2>URL utilizada para redirect</h2>
                                    <label> 
                                        <?php 
                                            echo ($gedSessionManager->GetHTTPRedirect());
                                        ?> 
                                    </label>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>

                <!-- se temos erros no manager da sessão mostrar:  -->
                <?php if ($gedSessionManager != null && $gedSessionManager->GetError() != null) {?> 
                    <div class="form-group">
                        <div class="well">
                            <h1>Algum erro aconteceu</h1>
                            <label>
                                <span style="white-space: normal;"> 
                                    <?php 
                                        echo ($gedSessionManager->GetError());
                                    ?>
                                </span>
                            </label>
                        </div>
                    </div>
                <?php } ?>
            </form>
        </div>
    </body>
</html>

