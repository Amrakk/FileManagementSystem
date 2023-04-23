function validateInput() {
    var fname = document.getElementById("first_name").value;
    var lname = document.getElementById("last_name").value;
    var email = document.getElementById("email").value;
    var user = document.getElementById("user").value;
    var password = document.getElementById("pass").value;
    var password_confirmation = document.getElementById("pass_confirm").value;

    if (fname == "") {
        showMessage("Please enter your first name");
        return false;
    } else if (lname == "") {
        showMessage("Please enter your last name");
        return false;
    } else if (
        email == "" ||
        email.indexOf("@") == -1 ||
        email.indexOf(".") == -1
    ) {
        showMessage("Please enter your email address");
        return false;
    } else if (user == "") {
        showMessage("Please enter your username");
        return false;
    } else if (password == "") {
        showMessage("Please enter your password");
        return false;
    } else if (password.length < 6 || password.length > 20) {
        showMessage("Password must be between 6 and 20 characters");
        return false;
    } else if (password_confirmation == "") {
        showMessage("Please confirm your password");
        return false;
    } else if (password != password_confirmation) {
        showMessage("Passwords do not match");
        return false;
    } else {
        clearMessage();
        return true;
    }
}
function showMessage(message) {
    var mess = document.getElementById("message");
    mess.innerHTML = message;
    mess.setAttribute("class", "alert alert-danger");
}
function clearMessage() {
    var mess = document.getElementById("message");
    mess.innerHTML = "";
    mess.setAttribute("class", "");
}
