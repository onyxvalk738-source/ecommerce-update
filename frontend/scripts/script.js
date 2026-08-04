var dir = document.getElementById('show-api');


console.log(dir);

var api = document.createElement('p');


dir.append(api);



async function get() {
    var response = await fetch('127.0.0.1:1337');

    var data = await response.json();

    api.innerHTML = data;

}



