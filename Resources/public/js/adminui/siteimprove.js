var SiteImproveAdminUIModule = function () {
    var _token = '';
    var _debug = true;
    var _timeout;
    var _publishClickableElements = [];

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

    function _getContainerData() {
        var $dataContainer = $("#js-siteimprove-data");
        if ($dataContainer.length > 0) {
            return JSON.parse($dataContainer.text());
        }
        return false;
    }

    function _init() {
        _addScript("https://cdn.siteimprove.net/cms/overlay.js", function () {
            var data = _getContainerData();
            if (data !== false) {
                if (_debug) {
                    console.log(data);
                }
                // on time on load
                _token = data.token;
                if (data.url && data.url !== '') {
                    _input(data.url);
                    return;
                }
            }
            _domain('');
        });

        $("button[id^=content_edit__sidebar_right__publish-tab]").click(function () {
            var data = _getContainerData();
            if (data !== false) {
                if (data.url && data.url !== '') {
                    _recheck(data.url);
                }
            }
            return true;
        });
        _change();
    }

    function _input(url, callback) {
        _call('input', url, callback);
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

    //@optim possible: Only way to do that, waiting information from eZ Systems to get an Handler/Listener/Something
    function _change() {
        clearTimeout(_timeout);
        _timeout = setTimeout(SiteImproveAdminUIModule.setClickCallOnPublish, 250);
        var hash = decodeURIComponent(window.location.hash);
        var parts = hash.split("/").filter(function (value) {
            return value.length > 0;
        });
        if (parts.slice(1, 3).join("/") === "studio/insite") {
            _input(parts.slice(3).join("/"));
        } else if (parts.slice(4, 7).join("/") === "studio/landing-page/dynamic") {
            _input(parts.slice(7).join("/"));
        }
    }

    //@optim possible: No way to do better here... waiting information from eZ Systems
    function _setClickCallOnPublish() {
        // Due to the current YUI implementation we have to listen in the hierarchy and test the target
        var clickableZones = [];
        document.querySelectorAll("div[data-selected-option=publish]").forEach(function (publishButton) {
            clickableZones.push(publishButton.parentNode);
        });

        var action = function () {
            var data = _getContainerData();
            if (data !== false) {
                if (data.url && data.url !== '') {
                    _recheck(data.url);
                }
            }
        };

        if (clickableZones.length >= 1) {
            clickableZones.forEach(function (clickableElt) {
                if (_publishClickableElements.indexOf(clickableElt) === -1) {
                    clickableElt.addEventListener("click", function (e) {
                        if (e.target.parentNode.parentNode.getAttribute('data-selected-option') === 'publish') {
                            action();
                        }
                    }, false);
                    _publishClickableElements.push(clickableElt);
                }
            });

            // if we find then, we stop the timeout
            return;
        }
        _timeout = setTimeout(SiteImproveAdminUIModule.setClickCallOnPublish, 250);

    }


    return {
        init: _init,
        input: _input,
        domain: _domain,
        recheck: _recheck,
        recrawl: _recrawl,
        clear: _clear,
        change: _change,
        setClickCallOnPublish: _setClickCallOnPublish
    };
}();


$(function () {
    SiteImproveAdminUIModule.init();
    window.addEventListener("hashchange", function (e) {
        SiteImproveAdminUIModule.change();
    }, false);
});
