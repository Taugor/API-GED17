// svcAuthentication: serviço que possuí variáveis para auxiliar na autorização
function svcAuthentication(){
    'use strict';
    
    //array auxiliar para realizar autorização
    var SuperHeroLookalikes = { 
        redtornado: 'vision',
        deathstroke: 'deadpool',
        bigbarda:	'gamora',
        redhood:	'wintersoldier',
        swampthing:	'manthing',
        greenarrow:	'hawkeye',
        aquaman:	'namor',
        elongatedman:	'mrfantastic',
        atom:	'antman',
        doompatrol:	'xmen',
        catwoman:	'blackcat',
        greenlantern:	'nova',
        batman:	'moonknight',
        wonderwoman:	'powerprincess',
        superman:	'hyperion'
    };

    return{
        SuperHeroLookalikes: SuperHeroLookalikes
    };
}

angular.module('app').factory('svcAuthentication', svcAuthentication);