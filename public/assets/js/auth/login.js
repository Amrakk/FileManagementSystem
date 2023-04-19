function validateInput() {
    var username = document.getElementById("username").value.trim();
    var password = document.getElementById("password").value.trim();
    var error = document.getElementById("error");

    if (username == "") {
        raiseError("Please enter your username");
        return false;
    } else if (password == "") {
        raiseError("Please enter your password");   
        return false;
    } else {
        error.innerHTML = "";
        return true;
    }
}

function raiseError(message) {
    var error = document.getElementById("error");
    error.classList.remove("d-none");
    error.innerHTML = message;
}