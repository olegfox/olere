(function () {
    var register = angular.module('register', []).config(function($interpolateProvider){
            $interpolateProvider.startSymbol('{[{').endSymbol('}]}');
        }
    );

    register.directive('passwordMatch', [function () {
        return {
            restrict: 'A',
            scope:true,
            require: 'ngModel',
            link: function (scope, elem , attrs,control) {
                var checker = function () {

                    //get the value of the first password
                    var e1 = scope.$eval(attrs.ngModel);

                    //get the value of the other password
                    var e2 = scope.$eval(attrs.passwordMatch);
                    return e1 == e2;
                };
                scope.$watch(checker, function (n) {

                    //set the form control to valid if both
                    //passwords are the same, else invalid
                    control.$setValidity("unique", n);
                });
            }
        };
    }]);

    register.controller('RegisterController', ['$scope', function ($scope) {
//        $scope.user = {
//            firstName: '',
//            inn: '',
//            nameCompany: '',
//            city: '',
//            email: '',
//            phone: '',
//            pass: '',
//            confirmPass: ''
//        };

        $scope.onlyNumbers = /^\d+$/;

//        $scope.phoneNumberPattern = (function() {
//            var regexp = /^\(?(\d{3})\)?[ .-]?(\d{7})$/;
//            return {
//                test: function(value) {
//                    if( $scope.requireTel === false ) return true;
//                    else return regexp.test(value);
//                }
//            };
//        })();

        $scope.submit=function(){

        }
    }]);

    $("#fos_user_registration_form_inn").mask("9999999999");

//    $("#fos_user_registration_form_phone").mask("(999) 9999999");

}());