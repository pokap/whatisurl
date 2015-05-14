
"use strict";

var path = function (mini) {
    return config.host + '/' + mini;
};

(function(document, undefined) {
    var app = angular.module('main', []);

    app.controller('SearchController', ['$http',
        function ($http) {
            var output = document.getElementById('output');
            var search = this;

            search.url = '';

            search.submit = function () {
                if (!validUrl(search.url)) {
                    showError('Please enter a valid URL.');
                    return;
                }

                blockInputs(true);

                var xhr = $http.get(path('search') + '?url=' + search.url);
                xhr.success(function(data, status) {
                    blockInputs(false);

                    // initialize
                    showError();

                    if (304 !== status) {
                        output.innerHTML = '';
                        output.appendChild(JsonHuman.format(data));
                    }
                });
                xhr.error(function(data, status) {
                    blockInputs(false);

                    output.innerHTML = '';

                    if (400 === status) {
                        showError('Bad url.');
                    }
                });
            };
        }
    ]);

    function getElement(id) {
        return document.getElementById(id);
    }

    function blockInputs(disable) {
        disable = !!disable;

        getElement('input-url').disabled = disable;
        getElement('button-search').disabled = disable;
    }

    function showError(message) {
        if (undefined === message) {
            message = '';
        } else {
            message = '<div class="alert alert-warning" role="alert">' + message + '</div>';
        }

        getElement('message-error').innerHTML = message;
    }

    function validUrl(str) {
        var pattern = new RegExp('^https?:\/\/'+            // protocol
        '((([a-z\d]([a-z\d-]*[a-z\d])*)\.)+[a-z]{2,}|'+     // domain name
        '((\d{1,3}\.){3}\d{1,3}))'+                         // OR ip (v4) address
        '(\:\d+)?(\/[-a-z\d%_.~+]*)*','i');                 // port and path

        return pattern.test(str);
    }
}(document, undefined));
