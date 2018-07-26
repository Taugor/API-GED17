(function(){
    'use strict';

    angular
        .module('app')
        .config(appConfig);

    appConfig.$inject = ['$routeProvider'];

    function appConfig($routeProvider) {
        $routeProvider
            .when('/', {
               template: '<ged-authentication></ged-authentication>'
            });
    }
}());