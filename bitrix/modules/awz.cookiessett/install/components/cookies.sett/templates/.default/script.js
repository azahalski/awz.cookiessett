(function() {
    'use strict';

    if (!!window.AwzCookiesSettComponent) {
        return;
    }
    window.AwzCookiesSettComponent_Instances = {};
    window.AwzCookiesSettComponent = function(options) {
        if(typeof options !== 'object') {
            throw new Error('options is not object');
        }
        if(!options.hasOwnProperty('siteId')) {
            throw new Error('options.siteId is required');
        }
        if(!options.hasOwnProperty('cmpId')) {
            throw new Error('options.cmpId is required');
        }
        if(!options.hasOwnProperty('templateName')) {
            throw new Error('options.templateName is required');
        }
        if(!options.hasOwnProperty('templateFolder')) {
            throw new Error('options.templateFolder is required');
        }
        if(!options.hasOwnProperty('signedParameters')) {
            throw new Error('options.signedParameters is required');
        }
        if(!options.hasOwnProperty('componentName')) {
            throw new Error('options.componentName is required');
        }
        this.cmpId = options.cmpId;
        this.siteId = options.siteId;
        this.templateName = options.templateName;
        this.templateFolder = options.templateFolder;
        this.componentName = options.componentName;
        this.signedParameters = options.signedParameters;
        this.ajaxTimer = (!!options.ajaxTimer ? options.ajaxTimer : false) || 100;
        this.debug = !!options.debug ? true : false;
        this.lang = (!!options.lang ? options.lang : false) || {};
        window.AwzCookiesSettComponent_Instances[this.cmpId] = this;

        let parent = this;

        BX.bind(BX('awz_cookies_sett__all'), 'click', function(e) {
            if(!!e) e.preventDefault();
            parent.allowAll();
        });
        BX.bind(BX('awz_cookies_sett__all_decline'), 'click', function(e) {
            if(!!e) e.preventDefault();
            parent.declineAll();
        });
        BX.bind(BX('awz_cookies_sett__settings'), 'click', function(e) {
            if(!!e) e.preventDefault();
            parent.settings();
        });
        BX.ready(function(){
            BX.bind(BX('awz_cookies_sett__settings_custom'), 'click', function(e) {
                if(!!e) e.preventDefault();
                parent.settings();
            });
        });
    };
    window.AwzCookiesSettComponent.prototype = {
        getInstance: function (cmpId) {
            if (!cmpId) {
                cmpId = 'default';
            }
            if (!window.AwzCookiesSettComponent_Instances.hasOwnProperty(cmpId)) {
                window.AwzCookiesSettComponent_Instances[cmpId] = this;
            }
            return window.AwzCookiesSettComponent_Instances[cmpId];
        },
        loc: function(code){
            return this.lang.hasOwnProperty(code) ? this.lang[code] : code;
        },
        getModal: function(content)
        {
            if(!!this._modal){
                return this._modal;
            }
            this._modal = BX.PopupWindowManager.create("popup-window-content-awz_cmp_cookies", null, {
                content: content,
                closeIcon : false,
                lightShadow : true,
                destroyed : true,
                fixed : true,
                maxWidth : 580,
                overlay: {
                    backgroundColor: '#000000', opacity: '80'
                }
            });
            return this._modal;
        },
        settings: function(){
            let parent = this;
            let formData = {};
            formData['signedParameters'] = parent.signedParameters;
            formData['method'] = 'POST';
            formData['componentName'] = parent.componentName;
            formData['templateName'] = parent.templateName;
            let elements = document.getElementsByClassName("awz_cookies_sett__message");
            if(elements && elements.length){
                elements[0].remove();
            }
            setTimeout(function(){
                BX.ajax.runComponentAction('awz:cookies.sett', 'getSett', {
                    mode: 'class',
                    data: formData
                }).then(function (response) {
                    if(response && response.hasOwnProperty('data') &&
                        response.hasOwnProperty('status') && response['status'] === 'success'){
                        parent.getModal(response['data']).show();

                        BX.bind(BX('awz_cookies_sett__save'), 'click', function(e) {
                            if(!!e) e.preventDefault();
                            parent.sendForm();
                        });

                    }
                }, function (response) {
                    console.error(response);
                });
            },this.ajaxTimer);
        },
        allowAll: function(){
            let formData = {
                'awz_cookies_mode1':'Y',
                'awz_cookies_mode2':'Y',
                'awz_cookies_mode3':'Y',
                'awz_cookies_mode_all':'Y'
            };
            formData['signedParameters'] = this.signedParameters;
            formData['method'] = 'POST';
            let elements = document.getElementsByClassName("awz_cookies_sett__message");
            if(elements && elements.length){
                elements[0].remove();
            }
            setTimeout(function(){
                BX.ajax.runComponentAction('awz:cookies.sett', 'allow', {
                    mode: 'class',
                    data: formData
                });
            },this.ajaxTimer);
        },
        declineAll: function(){
            let formData = {
                'awz_cookies_mode1':'N',
                'awz_cookies_mode2':'N',
                'awz_cookies_mode3':'N',
                'awz_cookies_mode_all':'N'
            };
            formData['signedParameters'] = this.signedParameters;
            formData['method'] = 'POST';
            document.getElementsByClassName("awz_cookies_sett__message")[0].remove();
            setTimeout(function(){
                BX.ajax.runComponentAction('awz:cookies.sett', 'allow', {
                    mode: 'class',
                    data: formData
                });
            },this.ajaxTimer);
        },
        sendForm: function(){
            let formData = new FormData(
                document.getElementById('awz_cookies_sett__detail-form'),
                document.getElementById('awz_cookies_sett__save')
            );
            formData.append('signedParameters', this.signedParameters);
            formData.append('method', 'POST');

            this.getModal().close();

            setTimeout(function(){
                BX.ajax.runComponentAction('awz:cookies.sett', 'allow', {
                    mode: 'class',
                    data: formData
                });
            },this.ajaxTimer);

        },
    };

})();