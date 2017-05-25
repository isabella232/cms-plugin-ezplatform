var SiteImproveLegacyModule = function () {
    var _token = '';
    var _debug = true;

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

        $("input[name^=PublishButton]").click(function() {
            var data = _getContainerData();
            if (data !== false) {
                if (data.url && data.url !== '') {
                    _recheck(data.url);
                }
            }
            return true;
        });
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

    return {
        init: _init,
        input: _input,
        domain: _domain,
        recheck: _recheck,
        recrawl: _recrawl,
        clear: _clear
    };
}();


$(function () {
    SiteImproveLegacyModule.init();
});
