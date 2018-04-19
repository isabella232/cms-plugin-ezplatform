window.addEventListener("hashchange", function (e) {
    SiteImproveUIModule.change();
}, false);

window.addEventListener("load", function (e) {
    SiteImproveUIModule.init();
}, false);

var SiteImproveUIModule = function () {
    var _token = '';
    var _debug = false;
    var _currentData = {token: '', url: ''};
    var _publishClickableElements = [];
    var _timeout;

    function _addScript(url, onload) {
        var script_tag = document.createElement('script');
        script_tag.setAttribute("type", "text/javascript");
        script_tag.setAttribute("src", url);
        if (script_tag.readyState) {
            script_tag.onreadystatechange = function () { // For old versions of IE
                if (this.readyState === 'complete' || this.readyState === 'loaded') {
                    onload();
                }
            };
        } else {
            script_tag.onload = onload;
        }
        (document.getElementsByTagName("head")[0] || document.documentElement).appendChild(script_tag);
    }

    function _get(url, callback) {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', url + "?" + Math.random());
        xhr.onload = function () {
            if (xhr.status === 200) {
                callback(xhr.responseText);
            }
        };
        xhr.send();
    }

    function _call(method, url, callback) {

        if (_debug) {
            console.log("SiteImprove(" + _token + "): Calling " + method + " with url = " + url);
        }
        var realCallback = function () {
            if (_debug) {
                console.log("SiteImprove(" + _token + "): Succeed " + method + " with url = " + url);
            }
            if (typeof callback === "function") {
                callback();
            }
        };

        var _si = window._si || [];
        if (method !== 'clear') {
            _si.push([method, url, _token, realCallback]);
        } else {
            _si.push([method, realCallback]);
        }
    }

    //@optim possible: No way to do better here... waiting information from eZ Systems
    function _setClickCallOnPublish() {
        // Due to the current YUI implementation we have to listen in the hierarchy and test the target
        var clickableZones = [];

        document.querySelectorAll("button[data-action=publish]").forEach(function (publishButton) {
            clickableZones.push(publishButton.parentNode);
        });

        document.querySelectorAll("div[data-selected-option=publish]").forEach(function (publishButton) {
            clickableZones.push(publishButton.parentNode);
        });

        var action = function () {
            if (_currentData !== false) {
                if (_currentData.url && _currentData.url !== '') {
                    _recheck(_currentData.url);
                    return;
                }
            }
        };

        if (clickableZones.length >= 1) {
            clickableZones.forEach(function (clickableElt) {
                if (_publishClickableElements.indexOf(clickableElt) === -1) {
                    clickableElt.addEventListener("click", function (e) {
                        if (
                            ( e.target.parentNode.parentNode.getAttribute('data-action') === 'publish') || // platform
                            ( e.target.parentNode.getAttribute('data-selected-option') === 'publish') // studio
                        ) {
                            action();
                        }
                    }, false);
                    _publishClickableElements.push(clickableElt);
                }
            });

            // if we find then, we stop the timeout
            return;
        }
        _timeout = setTimeout(SiteImproveUIModule.setClickCallOnPublish, 250);

    }

    function _init() {
        _addScript("https://cdn.siteimprove.net/cms/overlay.js", function () {
            _change();
        });
    }

    function _handleData(dataText) {
        _currentData = JSON.parse(dataText);
        if (_debug) {
            console.log(_currentData);
        }
        _token = _currentData.token;
        if (_currentData.url && _currentData.url !== '') {
            _input(_currentData.url);
            return;
        }
        _domain('');
    }

    function _handleDataTokenOnly(dataText) {
        _currentData = JSON.parse(dataText);
        _token = _currentData.token;
    }

    //@optim possible: Only way to do that, waiting information from eZ Systems to get an Handler/Listener/Something
    function _change() {
        clearTimeout(_timeout);
        _timeout = setTimeout(SiteImproveUIModule.setClickCallOnPublish, 250);
        var hash = decodeURIComponent(window.location.hash);
        var parts = hash.split("/").filter(function (value) {
            return value.length > 0;
        });

        if (parts.slice(1, 7).join("/") === "view/api/ezp/v2/content/locations") {
            _get("_siteimprove/l/" + parts[parts.length - 2] + "/" + parts[parts.length - 1], _handleData);
        } else if (parts.slice(1, 7).join("/") === "edit/api/ezp/v2/content/objects") {
            _get("_siteimprove/o/" + parts[parts.length - 2] + "/" + parts[parts.length - 1], _handleData);
        } else if (parts.slice(1, 3).join("/") === "studio/insite") {
            if (_token.length === 0) {
                _get("_siteimprove/t", function (datatext) {
                    _handleDataTokenOnly(datatext);
                    _input(parts.slice(3).join("/"));
                });
            } else {
                _input(parts.slice(3).join("/"));
            }
        } else if (parts.slice(1, 4).join("/") === "studio/landing-page/dynamic") {
            if (_token.length === 0) {
                _get("_siteimprove/t", function (dataText) {
                    _handleDataTokenOnly(dataText);
                    _input(parts.slice(4).join("/"));
                });
            } else {
                _input(parts.slice(4).join("/"));
            }
        } else {
            _get("_siteimprove/t", _handleData);
        }
    }

    function _input(url, callback) {
        _call('input', url, callback);
        _currentData.url = url;
    }

    function _domain(url, callback) {
        _call('domain', url, callback);
    }

    function _recheck(url, callback) {
        _call('recheck', url, callback);
    }

    function _recrawl(url, callback) {
        _call('recrawl', url, callback);
    }

    function _clear(callback) {
        _call('clear', '', callback);
    }

    return {
        init: _init,
        change: _change,
        input: _input,
        domain: _domain,
        recheck: _recheck,
        recrawl: _recrawl,
        clear: _clear,
        setClickCallOnPublish: _setClickCallOnPublish
    };
}();

