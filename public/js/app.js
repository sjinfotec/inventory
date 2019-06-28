/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "/";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 0);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/js/app.js":
/*!*****************************!*\
  !*** ./resources/js/app.js ***!
  \*****************************/
/*! no static exports found */
/***/ (function(module, exports) {

throw new Error("Module build failed (from ./node_modules/babel-loader/lib/index.js):\nSyntaxError: /var/www/html/laravel/package.json: Error while parsing JSON - Unexpected token < in JSON at position 40\n    at JSON.parse (<anonymous>)\n    at readConfigPackage (/var/www/html/laravel/node_modules/@babel/core/lib/config/files/package.js:57:20)\n    at /var/www/html/laravel/node_modules/@babel/core/lib/config/files/utils.js:29:12\n    at cachedFunction (/var/www/html/laravel/node_modules/@babel/core/lib/config/caching.js:33:19)\n    at findPackageData (/var/www/html/laravel/node_modules/@babel/core/lib/config/files/package.js:33:11)\n    at buildRootChain (/var/www/html/laravel/node_modules/@babel/core/lib/config/config-chain.js:105:85)\n    at loadPrivatePartialConfig (/var/www/html/laravel/node_modules/@babel/core/lib/config/partial.js:85:55)\n    at Object.loadPartialConfig (/var/www/html/laravel/node_modules/@babel/core/lib/config/partial.js:110:18)\n    at Object.<anonymous> (/var/www/html/laravel/node_modules/babel-loader/lib/index.js:144:26)\n    at Generator.next (<anonymous>)\n    at asyncGeneratorStep (/var/www/html/laravel/node_modules/babel-loader/lib/index.js:3:103)\n    at _next (/var/www/html/laravel/node_modules/babel-loader/lib/index.js:5:194)\n    at /var/www/html/laravel/node_modules/babel-loader/lib/index.js:5:364\n    at new Promise (<anonymous>)\n    at Object.<anonymous> (/var/www/html/laravel/node_modules/babel-loader/lib/index.js:5:97)\n    at Object.loader (/var/www/html/laravel/node_modules/babel-loader/lib/index.js:60:18)\n    at Object.<anonymous> (/var/www/html/laravel/node_modules/babel-loader/lib/index.js:55:12)");

/***/ }),

/***/ "./resources/sass/app.scss":
/*!*********************************!*\
  !*** ./resources/sass/app.scss ***!
  \*********************************/
/*! no static exports found */
/***/ (function(module, exports) {

throw new Error("Module build failed (from ./node_modules/css-loader/index.js):\nModuleBuildError: Module build failed (from ./node_modules/sass-loader/lib/loader.js):\n\n@import url('https://fonts.googleapis.com/css?family=Nunito');\n                                                            ^\n      Expected \"{\".\n  ╷\n4 │ @import url('https://fonts.googleapis.com/css?family=Nunito');\n  │                                                              ^\n  ╵\n  stdin 4:62  root stylesheet\n      in /var/www/html/laravel/resources/sass/app.scss (line 4, column 62)\n    at runLoaders (/var/www/html/laravel/node_modules/webpack/lib/NormalModule.js:302:20)\n    at /var/www/html/laravel/node_modules/loader-runner/lib/LoaderRunner.js:367:11\n    at /var/www/html/laravel/node_modules/loader-runner/lib/LoaderRunner.js:233:18\n    at context.callback (/var/www/html/laravel/node_modules/loader-runner/lib/LoaderRunner.js:111:13)\n    at render (/var/www/html/laravel/node_modules/sass-loader/lib/loader.js:52:13)\n    at Function.$2 (/var/www/html/laravel/node_modules/sass/sass.dart.js:24300:48)\n    at yb.$2 (/var/www/html/laravel/node_modules/sass/sass.dart.js:15220:15)\n    at wf.lI (/var/www/html/laravel/node_modules/sass/sass.dart.js:9168:42)\n    at wf.lH (/var/www/html/laravel/node_modules/sass/sass.dart.js:9170:32)\n    at iQ.w2 (/var/www/html/laravel/node_modules/sass/sass.dart.js:8439:46)\n    at vC.$0 (/var/www/html/laravel/node_modules/sass/sass.dart.js:8575:7)\n    at Object.eU (/var/www/html/laravel/node_modules/sass/sass.dart.js:1461:80)\n    at al.bc (/var/www/html/laravel/node_modules/sass/sass.dart.js:8492:3)\n    at j3.bc (/var/www/html/laravel/node_modules/sass/sass.dart.js:8434:25)\n    at j3.cA (/var/www/html/laravel/node_modules/sass/sass.dart.js:8421:12)\n    at pV.cA (/var/www/html/laravel/node_modules/sass/sass.dart.js:8203:35)\n    at Object.m (/var/www/html/laravel/node_modules/sass/sass.dart.js:1351:19)\n    at /var/www/html/laravel/node_modules/sass/sass.dart.js:5080:51\n    at yG.a (/var/www/html/laravel/node_modules/sass/sass.dart.js:1359:71)\n    at yG.$2 (/var/www/html/laravel/node_modules/sass/sass.dart.js:8222:23)\n    at xd.$2 (/var/www/html/laravel/node_modules/sass/sass.dart.js:8217:25)\n    at wf.lI (/var/www/html/laravel/node_modules/sass/sass.dart.js:9168:42)\n    at wf.lH (/var/www/html/laravel/node_modules/sass/sass.dart.js:9170:32)\n    at iQ.w2 (/var/www/html/laravel/node_modules/sass/sass.dart.js:8439:46)\n    at vC.$0 (/var/www/html/laravel/node_modules/sass/sass.dart.js:8575:7)\n    at Object.eU (/var/www/html/laravel/node_modules/sass/sass.dart.js:1461:80)\n    at al.bc (/var/www/html/laravel/node_modules/sass/sass.dart.js:8492:3)\n    at j3.bc (/var/www/html/laravel/node_modules/sass/sass.dart.js:8434:25)\n    at j3.cA (/var/www/html/laravel/node_modules/sass/sass.dart.js:8421:12)\n    at pW.$0 (/var/www/html/laravel/node_modules/sass/sass.dart.js:8210:24)\n    at Object.JO (/var/www/html/laravel/node_modules/sass/sass.dart.js:1489:5)\n    at o1.K7 (/var/www/html/laravel/node_modules/sass/sass.dart.js:1491:7)\n    at q_.$0 (/var/www/html/laravel/node_modules/sass/sass.dart.js:8167:22)\n    at Lb (/var/www/html/laravel/node_modules/sass/sass.dart.js:565:52)\n    at Immediate.<anonymous> (/var/www/html/laravel/node_modules/sass/sass.dart.js:574:51)\n    at processImmediate (internal/timers.js:443:21)");

/***/ }),

/***/ 0:
/*!*************************************************************!*\
  !*** multi ./resources/js/app.js ./resources/sass/app.scss ***!
  \*************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(/*! /var/www/html/laravel/resources/js/app.js */"./resources/js/app.js");
module.exports = __webpack_require__(/*! /var/www/html/laravel/resources/sass/app.scss */"./resources/sass/app.scss");


/***/ })

/******/ });