webpackJsonp([1],{

/***/ 350:
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

__webpack_require__(374);

__webpack_require__(73);

var _LOGO = __webpack_require__(74);

var _LOGO2 = _interopRequireDefault(_LOGO);

var _erweima = __webpack_require__(75);

var _erweima2 = _interopRequireDefault(_erweima);

var _listContainer = __webpack_require__(375);

var _listContainer2 = _interopRequireDefault(_listContainer);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

(0, _jquery2.default)(".tzidc-content-list").empty();
_reactDom2.default.render(_react2.default.createElement(_listContainer2.default, null), (0, _jquery2.default)(".tzidc-content-list")[0]);
_reactDom2.default.render(_react2.default.createElement('img', { src: _LOGO2.default }), (0, _jquery2.default)("#tzidc_logo")[0]);
_reactDom2.default.render(_react2.default.createElement('img', { src: _erweima2.default }), (0, _jquery2.default)(".tzidc-wx-saoma .erweima")[0]);
_reactDom2.default.render(_react2.default.createElement('img', { src: __webpack_require__(76) }), (0, _jquery2.default)(".modal-tip-body .modal-tip-icon")[0]);

/***/ }),

/***/ 374:
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ 375:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.default = undefined;

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _react = __webpack_require__(30);

var _react2 = _interopRequireDefault(_react);

var _listTab = __webpack_require__(376);

var _listTab2 = _interopRequireDefault(_listTab);

var _list = __webpack_require__(377);

var _list2 = _interopRequireDefault(_list);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

var ListContainer = function (_Component) {
    _inherits(ListContainer, _Component);

    function ListContainer(props) {
        _classCallCheck(this, ListContainer);

        var _this = _possibleConstructorReturn(this, (ListContainer.__proto__ || Object.getPrototypeOf(ListContainer)).call(this, props));

        _this.state = {
            id: "all"
        };
        return _this;
    }

    _createClass(ListContainer, [{
        key: "switchList",
        value: function switchList(id) {
            // console.log(id);
            this.setState({
                id: id
            });
        }
    }, {
        key: "render",
        value: function render() {
            return _react2.default.createElement(
                "div",
                null,
                _react2.default.createElement(_listTab2.default, { switchlistfn: this.switchList.bind(this) }),
                _react2.default.createElement(_list2.default, { tabid: this.state.id })
            );
        }
    }]);

    return ListContainer;
}(_react.Component);

exports.default = ListContainer;

/***/ }),

/***/ 376:
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function($) {

Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.default = undefined;

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _react = __webpack_require__(30);

var _react2 = _interopRequireDefault(_react);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

var ListTab = function (_Component) {
    _inherits(ListTab, _Component);

    function ListTab(props) {
        _classCallCheck(this, ListTab);

        var _this = _possibleConstructorReturn(this, (ListTab.__proto__ || Object.getPrototypeOf(ListTab)).call(this, props));

        _this.state = {};
        _this.getData(function (data) {
            _this.setState({
                tabData: data.map(function (item) {
                    return _react2.default.createElement(
                        "li",
                        { role: "presentation", key: item.id },
                        _react2.default.createElement(
                            "a",
                            { href: "#cyber_security", onClick: function onClick() {
                                    return _this.props.switchlistfn(item.id);
                                }, "aria-controls": "cyber security", role: "tab", "data-toggle": "tab" },
                            item.name
                        )
                    );
                })
            });
        });
        return _this;
    }

    _createClass(ListTab, [{
        key: "getData",
        value: function getData(callbrak) {
            $.ajax({
                url: "/tab.json",
                type: "GET",
                success: function success(data) {
                    callbrak(data);
                }
            });
        }
    }, {
        key: "render",
        value: function render() {
            var _this2 = this;

            return _react2.default.createElement(
                "ul",
                { className: "nav nav-tabs", role: "tablist" },
                _react2.default.createElement(
                    "li",
                    { role: "presentation", className: "active" },
                    _react2.default.createElement(
                        "a",
                        { href: "#latest_articles", onClick: function onClick() {
                                return _this2.props.switchlistfn('all');
                            }, "aria-controls": "latest articles", role: "tab", "data-toggle": "tab" },
                        "\u6700\u65B0\u6587\u7AE0"
                    )
                ),
                this.state.tabData
            );
        }
    }]);

    return ListTab;
}(_react.Component);

exports.default = ListTab;
/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(9)))

/***/ }),

/***/ 377:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.default = undefined;

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

var _react = __webpack_require__(30);

var _react2 = _interopRequireDefault(_react);

var _jquery = __webpack_require__(9);

var _jquery2 = _interopRequireDefault(_jquery);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

var List = function (_Component) {
    _inherits(List, _Component);

    function List(props) {
        _classCallCheck(this, List);

        var _this = _possibleConstructorReturn(this, (List.__proto__ || Object.getPrototypeOf(List)).call(this, props));

        _this.state = {};
        _this.getData(function (data) {
            _this.setState({
                listdata: data
            });
        });

        _this.colorDict = {
            "1": "green",
            "2": "orange",
            "3": "purple",
            "4": "blue",
            "5": "black"
        };
        return _this;
    }

    _createClass(List, [{
        key: 'getData',
        value: function getData(callbrak) {
            var _this2 = this;

            if (!this.listdata) {
                _jquery2.default.ajax({
                    url: "/list.json",
                    type: "GET",
                    success: function success(data) {
                        _this2.listdata = data;
                        callbrak(data);
                    }
                });
            } else {
                callbrak(this.listdata);
            }
        }
    }, {
        key: 'render',
        value: function render() {
            var _this3 = this;

            var listEl = null;
            if (this.state.listdata) {
                listEl = this.state.listdata.map(function (item) {
                    if (_this3.props.tabid == "all") {
                        return _react2.default.createElement(
                            'li',
                            { key: item.id },
                            _react2.default.createElement(
                                'div',
                                { className: 'media' },
                                _react2.default.createElement(
                                    'div',
                                    { className: 'media-left' },
                                    _react2.default.createElement(
                                        'a',
                                        { href: item.link },
                                        _react2.default.createElement('img', { className: 'media-object', src: item.thumbnail,
                                            alt: '...' })
                                    )
                                ),
                                _react2.default.createElement(
                                    'div',
                                    { className: 'media-body' },
                                    _react2.default.createElement(
                                        'h4',
                                        { className: 'media-heading' },
                                        _react2.default.createElement(
                                            'a',
                                            { href: item.link },
                                            _react2.default.createElement(
                                                'span',
                                                { className: "tip " + _this3.colorDict[item.tid + ''] },
                                                item.tab
                                            ),
                                            item.title
                                        )
                                    ),
                                    _react2.default.createElement(
                                        'p',
                                        { className: 'margin-none' },
                                        item.descriptions
                                    ),
                                    _react2.default.createElement(
                                        'div',
                                        { className: 'media-body-footer' },
                                        _react2.default.createElement(
                                            'time',
                                            { className: 'pull-left' },
                                            item.date
                                        ),
                                        _react2.default.createElement(
                                            'p',
                                            { className: 'tzidc-info-count pull-right' },
                                            _react2.default.createElement(
                                                'span',
                                                { className: 'tzidc-read-count' },
                                                item.view
                                            ),
                                            _react2.default.createElement(
                                                'span',
                                                { className: 'tzidc-info-count' },
                                                item.info
                                            )
                                        )
                                    )
                                )
                            )
                        );
                    }
                    if (item.tid == _this3.props.tabid) {
                        return _react2.default.createElement(
                            'li',
                            { key: item.id },
                            _react2.default.createElement(
                                'div',
                                { className: 'media' },
                                _react2.default.createElement(
                                    'div',
                                    { className: 'media-left' },
                                    _react2.default.createElement(
                                        'a',
                                        { href: item.link },
                                        _react2.default.createElement('img', { className: 'media-object', src: item.thumbnail,
                                            alt: '...' })
                                    )
                                ),
                                _react2.default.createElement(
                                    'div',
                                    { className: 'media-body' },
                                    _react2.default.createElement(
                                        'h4',
                                        { className: 'media-heading' },
                                        _react2.default.createElement(
                                            'a',
                                            { href: item.link },
                                            _react2.default.createElement(
                                                'span',
                                                { className: "tip " + _this3.colorDict[item.tid + ''] },
                                                item.tab
                                            ),
                                            item.title
                                        )
                                    ),
                                    _react2.default.createElement(
                                        'p',
                                        { className: 'margin-none' },
                                        item.descriptions
                                    ),
                                    _react2.default.createElement(
                                        'div',
                                        { className: 'media-body-footer' },
                                        _react2.default.createElement(
                                            'time',
                                            { className: 'pull-left' },
                                            item.date
                                        ),
                                        _react2.default.createElement(
                                            'p',
                                            { className: 'tzidc-info-count pull-right' },
                                            _react2.default.createElement(
                                                'span',
                                                { className: 'tzidc-read-count' },
                                                item.view
                                            ),
                                            _react2.default.createElement(
                                                'span',
                                                { className: 'tzidc-info-count' },
                                                item.info
                                            )
                                        )
                                    )
                                )
                            )
                        );
                    }
                });
            }

            return _react2.default.createElement(
                'div',
                null,
                _react2.default.createElement(
                    'div',
                    { className: 'tab-content' },
                    _react2.default.createElement(
                        'div',
                        { role: 'tabpanel', className: 'tab-pane active', id: 'latest_articles' },
                        _react2.default.createElement(
                            'ul',
                            null,
                            listEl
                        )
                    ),
                    _react2.default.createElement(
                        'div',
                        { role: 'tabpanel', className: 'tab-pane', id: 'cyber_security' },
                        _react2.default.createElement(
                            'ul',
                            null,
                            listEl
                        )
                    ),
                    _react2.default.createElement(
                        'div',
                        { role: 'tabpanel', className: 'tab-pane', id: 'data_center' },
                        _react2.default.createElement(
                            'ul',
                            null,
                            listEl
                        )
                    ),
                    _react2.default.createElement(
                        'div',
                        { role: 'tabpanel', className: 'tab-pane', id: 'cloud_computing' },
                        _react2.default.createElement(
                            'ul',
                            null,
                            listEl
                        )
                    ),
                    _react2.default.createElement(
                        'div',
                        { role: 'tabpanel', className: 'tab-pane', id: 'big_data' },
                        _react2.default.createElement(
                            'ul',
                            null,
                            listEl
                        )
                    ),
                    _react2.default.createElement(
                        'div',
                        { role: 'tabpanel', className: 'tab-pane', id: 'CDN' },
                        _react2.default.createElement(
                            'ul',
                            null,
                            listEl
                        )
                    )
                ),
                _react2.default.createElement(
                    'div',
                    { className: 'more' },
                    _react2.default.createElement(
                        'a',
                        { href: this.props.tabid != "all" ? "/list.html?tid=" + this.props.tabid : "#" },
                        '\u70B9\u51FB\u66F4\u591A'
                    )
                )
            );
        }
    }]);

    return List;
}(_react.Component);

exports.default = List;

/***/ })

},[350]);