/**
 * Created by deusdeorum on 10.05.15.
 */

var app = angular.module('app', [
    'ngRoute',
    'mgcrea.ngStrap',
    'appControllers',
    'angular-loading-bar'
]);

app.config(['$routeProvider', '$httpProvider',
    function($routeProvider, $httpProvider) {
        $routeProvider.
            when('/', {
                templateUrl: 'partials/login.html',
                controller: 'LoginCtrl'
            }).
            when('/best', {
                templateUrl: 'partials/best.html',
                controller: 'ResultsListCtrl'
            }).
            when('/mistakes', {
                templateUrl: 'partials/mistakes.html',
                controller: 'MistakeListCtrl'
            }).
            when('/test', {
                templateUrl: 'partials/test.html',
                controller: 'TestCtrl'
            }).
            otherwise({
                templateUrl: 'partials/404.html'
            });
        $httpProvider.interceptors.push('authInterceptor');
    }
]);

app.factory('authInterceptor', function ($q, $window, $location) {
    return {
        request: function (config) {
            if ($window.sessionStorage.token) {
                //HttpBearerAuth
                config.headers.Authorization = 'Bearer ' + $window.sessionStorage.token;
            }
            return config;
        },
        responseError: function (rejection) {
            if (rejection.status === 401) {
                $location.path('/').replace();
            }
            return $q.reject(rejection);
        }
    };
});