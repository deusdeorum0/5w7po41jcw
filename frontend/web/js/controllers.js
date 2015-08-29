'use strict';

/* Controllers */

var appControllers = angular.module('appControllers', []);

appControllers.controller('MistakeListCtrl', ['$scope', '$http',
    function ($scope, $http) {
        $http.get('api.dict.skyeng.local/mistakes').success(function(data) {
            $scope.mistakes = data;
        });
    }]);

appControllers.controller('ResultsListCtrl', ['$scope', '$http',
    function ($scope, $http) {
        $http.get('api.dict.skyeng.local/results').success(function(data) {
            $scope.results = data;
        });
    }]);

appControllers.controller('LoginCtrl', ['$scope', '$http', '$window', '$location',
    function($scope, $http, $window, $location) {
        $scope.login = function () {
            $scope.submitted = true;
            $scope.error = {};
            $http.post('api.dict.skyeng.local/login', {name: $scope.username}).success(
                function (data) {
                    if (data.token) {
                        $window.sessionStorage.token = data.token;
                        $location.path('/test').replace();
                    } else {
                        angular.forEach(data, function (error, field) {
                            $scope.error[field] = error[0];
                        });
                    }
                }
            );
        };
    }
]);

appControllers.controller('TestCtrl', ['$scope', '$http', '$window', '$location',
    function ($scope, $http, $window, $location) {

        function getTask() {
            $http.post('api.dict.skyeng.local/task').success(
                function (data) {
                    $scope.question = data.question;
                    $scope.options = data.options;
                    $scope.wrong = [];
                }
            );

        }

        if (!$window.sessionStorage.token) {
            $location.path('/').replace();
        } else {
            $scope.correct = 0;
            $scope.mistakes = 0;
            $scope.finished = false;
            $scope.answered = false;

            getTask();
        }

        $scope.answer = function (index) {
            if (!$scope.answered) {
                $scope.answered = true;
                var optionID = $scope.options[index]._id['$id'];
                $http.post('api.dict.skyeng.local/answer', {_id: optionID}).success(
                    function (data) {
                        $scope.correct = data.correct;
                        $scope.mistakes = data.mistakes;

                        if (data.finished) {
                            $scope.finished = true;
                            $window.sessionStorage.token = false;
                        } else {
                            if (data.result) {
                                getTask();
                            } else {
                                $scope.wrong[index] = true;
                            }
                            $scope.answered = false;
                        }
                    }
                );
            }
        };
    }]);