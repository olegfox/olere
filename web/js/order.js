(function () {
    var order = angular.module('order', []).config(function($interpolateProvider){
            $interpolateProvider.startSymbol('{[{').endSymbol('}]}');
        }
    );

    order.controller('OrderController', ['$scope', function ($scope) {
        $scope.ord = {
            city : '',
            delivery : 0,
            address : '',
            transport : '',
            username : '',
            email : '',
            phone : '',
            timeBegin : '',
            timeEnd : '',
            comment: ''
        };
        if(window.ord){
           $scope.ord.city = window.ord.city;
           $scope.ord.username = window.ord.username;
           $scope.ord.email = window.ord.email;
           $scope.ord.phone = window.ord.phone;
           $scope.ord.address = window.ord.address;
        }
        $scope.onlyNumbers = /^\d+$/;

        $scope.$watch('ord', function() {
            $("input.ng-valid, select.ng-valid").each(function(){
                $(this).parent().find("span.valid").css("display", 'inline-block');
            });
            $("input.ng-invalid, select.ng-invalid").each(function(){
                $(this).parent().find("span.valid").css("display", 'none');
            });
        }, true);

        $scope.submit=function(){

        }
    }]);

}());