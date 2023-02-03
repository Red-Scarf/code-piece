'use strict';

var count = 0, oldParseInt = parseInt;

window.parseInt = function () {
    count += 1;
    // apply 接受2个参数 第一个是指定哪个对象为this
    return oldParseInt.apply(null, arguments);
}

console.log(parseInt('123'));
console.log(parseInt('aaaa'));
console.log(count);

console.log(parseInt.call(null, '111', 2));