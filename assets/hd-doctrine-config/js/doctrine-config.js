'use strict';


var module = angular.module('ag-admin');

module.config(['$routeProvider', '$provide', function($routeProvider, $provide) {
    // setup the API Base Path (this should come from initial ui load/php)
    $provide.value('apiBasePath', angular.element('body').data('api-base-path') || '/admin/api');


    $routeProvider.when('/global/doctrine-adapters', {
        templateUrl: 'hd-doctrine-config/partials/global/doctrine-adapters.html',
        controller: 'DoctrineAdapterController'
    });
}]);

module.factory('DoctrineAdapterResource', ['$http', '$q', '$location', 'apiBasePath', function ($http, $q, $location, apiBasePath) {

    var doctrineAdapterApiPath = apiBasePath + '/doctrine-adapter';

    var resource =  new Hyperagent.Resource(doctrineAdapterApiPath);

    resource.getList = function () {
        var deferred = $q.defer();

        this.fetch().then(function (adapters) {
            console.log(adapters);
            var doctrineAdapters = _.pluck(adapters.embedded.doctrine_adapter, 'props');
            deferred.resolve(doctrineAdapters);
        });

        return deferred.promise;
    };

    resource.saveAdapter = function (name, data) {
        return $http({method: 'patch', url: doctrineAdapterApiPath + '/' + encodeURIComponent(name), data: data})
            .then(function (response) {
                return response.data;
            });
    };

    resource.removeAdapter = function (name) {
        return $http.delete(doctrineAdapterApiPath + '/' + encodeURIComponent(name))
            .then(function (response) {
                return true;
            });
    };

    return resource;
}]);

module.controller(
    'DoctrineAdapterController',
    ['$scope', '$location', 'flash', 'DoctrineAdapterResource', function ($scope, $location, flash, DoctrineAdapterResource) {
        $scope.doctrineAdapters = [];
        $scope.showNewDoctrineoctrinedapterForm = false;

        $scope.resetForm = function () {
            $scope.showNewDoctrineAdapterForm = false;
            $scope.adapterName = '';
            $scope.driver      = '';
            $scope.database    = '';
            $scope.username    = '';
            $scope.password    = '';
            $scope.hostname    = 'localhost';
            $scope.port        = '';
        };

        function updateDoctrineAdapters(force) {
            $scope.doctrineAdapters = [];
            DoctrineAdapterResource.fetch({force: force}).then(function (doctrineAdapters) {
                console.log(doctrineAdapters);
                $scope.$apply(function () {
                    $scope.doctrineAdapters = _.pluck(doctrineAdapters.embedded.doctrine_adapter, 'props');
                });
            });
        }
        updateDoctrineAdapters(false);

        $scope.saveDoctrineAdapter = function (index) {
            var doctrineAdapter = $scope.doctrineAdapters[index];
            var options = {
                driver      :  doctrineAdapter.params.driver,
                dbname      :  doctrineAdapter.params.dbname,
                user        :  doctrineAdapter.params.user,
                password    :  doctrineAdapter.params.password,
                host        :  doctrineAdapter.params.host,
                port        :  doctrineAdapter.params.port
            };
            DoctrineAdapterResource.saveAdapter(doctrineAdapter.adapter_name, options).then(function (doctrineAdapter) {
                flash.success = 'Database adapter ' + doctrineAdapter.adapter_name + ' updated';
                updateDoctrineAdapters(true);
            });
        };

        $scope.removeDoctrineAdapter = function (adapter_name) {
            DoctrineAdapterResource.removeAdapter(adapter_name).then(function () {
                flash.success = 'Database adapter ' + adapter_name + ' reset';
                updateDoctrineAdapters(true);
                $scope.deleteDoctrineAdapter = false;
            });
        };
    }]
);