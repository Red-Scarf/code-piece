'use strict';
// 赋值 Destructuring assignment

// 数组的解构赋值
var arr = ['1', '2', '3', [4, [11, 12], 5]];
// 可以存在空位 可以多维数组
var [a, , c, [d, [f, g], e]] = arr;


// 对象的解构赋值
var person = {
    name: '小明',
    age: 20,
    gender: 'male',
    passport: 'G-12345678',
    school: 'No.4 middle school',
    address: {
        city: 'Beijing',
        street: 'No.1 Road',
        zipcode: '100001'
    }
};
// 不能存在空位 可以嵌套 用冒号指定赋值的变量名 用等号指定默认值
var { name:n, age, sex:s='man', school, address: { city, zipcode } } = person;
console.log(n, age, s, school, city, zipcode);