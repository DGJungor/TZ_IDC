webpackJsonp([2],{

/***/ 380:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _react = __webpack_require__(30);

var _react2 = _interopRequireDefault(_react);

var _reactDom = __webpack_require__(69);

var _reactDom2 = _interopRequireDefault(_reactDom);

var _jquery = __webpack_require__(9);

var _jquery2 = _interopRequireDefault(_jquery);

__webpack_require__(70);

__webpack_require__(71);

__webpack_require__(72);

__webpack_require__(381);

__webpack_require__(73);

__webpack_require__(382);

var _LOGO = __webpack_require__(74);

var _LOGO2 = _interopRequireDefault(_LOGO);

var _erweima = __webpack_require__(75);

var _erweima2 = _interopRequireDefault(_erweima);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

_reactDom2.default.render(_react2.default.createElement('img', { src: _LOGO2.default }), (0, _jquery2.default)("#tzidc_logo")[0]);
_reactDom2.default.render(_react2.default.createElement('img', { src: _erweima2.default }), (0, _jquery2.default)(".tzidc-wx-saoma .erweima")[0]);
_reactDom2.default.render(_react2.default.createElement('img', { src: __webpack_require__(76) }), (0, _jquery2.default)(".modal-tip-body .modal-tip-icon")[0]);

/***/ }),

/***/ 381:
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ 382:
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function($) {

$("#full_page").click(function () {
    $("body,html").scrollTop(0);
    $('#main_header').css("position", "static");
    var copyContentContainer = $("#content_container").clone(true);
    copyContentContainer.css({
        "width": 850,
        "margin": "0 auto"
    });
    $("body").css({
        "height": "100%",
        "overflow": "hidden"
    });
    $(".article-mark").css({
        "display": "block"
    }).append(copyContentContainer);
});
$("#retop").click(function () {
    $("html,body").animate({
        scrollTop: 0
    }, 500);
});
/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(9)))

/***/ })

},[380]);