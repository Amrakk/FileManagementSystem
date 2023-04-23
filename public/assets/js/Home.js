$(document).ready(function() {

    $('#create-folder-btn').click(function() {
      // Your custom code here
        createNewFolder();
        $('#message-dialog').on('hidden.bs.modal', function () {
            location.reload();
        });
    });
});



function createNewFolder() {
    $('#new-folder-dialog').modal('hide');
    title = "Create Folder";
    fetch('http://localhost/api/storage/create_folder', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
            },
            body: JSON.stringify({ 
                folder_name: $('#folder-name').val(),
                folder_path: $('#current-path').val() 
            })
        }).then(function(response) {
            return response.json();
        }).then(function(data) {
            console.log(data);
            showMessage(title, data.message);
        }).catch(error => showMessage(title, error)
    );
}

function showMessage(title, message) {
    $('#message-title').text(title);
    $('#message-body').text(message);
    $('#message-dialog').modal('show');
}