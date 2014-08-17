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
                    $("input.ng-valid, select.ng-valid").each(function(){
                        $(this).parent().find("span.valid").css("display", 'inline-block');
                    });
                    $("input.ng-invalid, select.ng-invalid").each(function(){
                        $(this).parent().find("span.valid").css("display", 'none');
                    });
                });
            }
        };
    }]);

    register.controller('RegisterController', ['$scope', function ($scope) {
        $scope.user = {
            firstName: '',
            inn: '',
            nameCompany: '',
            city: '',
            email: '',
            phone: '',
            password: '',
            passwordConfirm: '',
            formCompany : '',
            profileCompany : '',
            countPoint : '',
            organization : '',
            kpp : '',
            currentAccount : '',
            bank : '',
            correspondentAccount : '',
            bik : ''
        };

        if(window.user){
            if(window.user.firstName){
                $scope.user.firstName = window.user.firstName;
            }
            if(window.user.inn){
                $scope.user.inn = window.user.inn;
            }
            if(window.user.nameCompany){
                $scope.user.nameCompany = window.user.nameCompany;
            }
            if(window.user.city){
                $scope.user.city = window.user.city;
            }
            if(window.user.email){
                $scope.user.email = window.user.email;
            }
            if(window.user.phone){
                $scope.user.phone = window.user.phone;
            }
            if(window.user.formCompany){
                $scope.user.formCompany = window.user.formCompany;
            }
            if(window.user.profileCompany){
                $scope.user.profileCompany = window.user.profileCompany;
            }
            if(window.user.countPoint){
                $scope.user.countPoint = window.user.countPoint;
            }
            if(window.user.organization){
                $scope.user.organization = window.user.organization;
            }
            if(window.user.kpp){
                $scope.user.kpp = window.user.kpp;
            }
            if(window.user.currentAccount){
                $scope.user.currentAccount = window.user.currentAccount;
            }
            if(window.user.bank){
                $scope.user.bank = window.user.bank;
            }
            if(window.user.correspondentAccount){
                $scope.user.correspondentAccount = window.user.correspondentAccount;
            }
            if(window.user.bik){
                $scope.user.bik = window.user.bik;
            }
        }

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
        $scope.$watch('user', function() {
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

    $("#fos_user_registration_form_inn").bind("change keyup input click", function() {
        if (this.value.match(/[^0-9]/g)) {
            this.value = this.value.replace(/[^0-9]/g, '');
        }
    });

//    $("#fos_user_registration_form_phone").mask("(999) 9999999");

}());