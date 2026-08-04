var dir = document.getElementById('show-api');
var buttom = document.getElementById('get');


console.log(dir);

var api = document.createElement('p');


dir.append(api);





async function get() {
    var response = await fetch('127.0.0.1:1337');

    var data = await response.json();
    console.log(data);


    api.innerHTML = data;

}

buttom.addEventListener("click", () => {
    get();
})



