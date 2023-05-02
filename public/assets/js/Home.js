$(document).ready(async function () {

    // Load
    const current_path = $("#current-path").val();
    const file_content = await getFileFolder(current_path);
    const display_content = await getDisplayContent(file_content);

    loadBreadcrumb(current_path);
    displayFileContent(display_content);

    // Add file or folder name to modal
    $("i.fa.fa-trash.action").click(function () {
        const filename = $(this).data("filename");
        $("#confirm-delete").find("strong").text(filename);
    });

    // Add file or folder name to modal
    $("i.fa.fa-edit.action").click(function () {
        const filename = $(this).data("filename");
        $("#confirm-rename").find("strong").text(filename);
    });

    // Download file
    $("i.fa.fa-download.action").click(function () {
        const filename = $(this).data("filename");
        downloadFile(filename, $("#current-path").val());
    });

    // Create new folder
    $("#create-folder-btn").click(function () {
        createNewFolder($("#folder-name").val(), $("#current-path").val());
        $("#message-dialog").on("hidden.bs.modal", function () {
            location.reload();
        });
    });

    // Create new file
    $("#create-file-btn").click(function () {
        createNewFile(new FormData($("#new-file-form").get(0)));
        $("#message-dialog").on("hidden.bs.modal", function () {
            location.reload();
        });
    });

    // Delete file or folder
    $("#delete-btn").click(function () {
        deleteFileFolder(
            $("#current-path").val() +
                "/" +
                $("#confirm-delete").find("strong").text()
        );
        $("#message-dialog").on("hidden.bs.modal", function () {
            location.reload();
        });
    });

    // Rename file or folder
    $("#rename-btn").click(function () {
        renameFileFolder(
            $("#current-path").val() +
                "/" +
                $("#confirm-rename").find("strong").text(),
            $("#new-name").val()
        );
        $("#message-dialog").on("hidden.bs.modal", function () {
            location.reload();
        });
    });

    const fileList = [];

    // Listen for changes in the file input
    $("#file-input").on("change", function (event) {
        const files = $(this)[0].files;

        for (let i = 0; i < files.length; i++) {
            fileList.push(files[i]);
            $(".file-list").append(`
                <div class="file-item border rounded p-2 mb-2 d-flex justify-content-between align-items-center">
                    <p class="mb-0">${files[i].name}</p>
                    <button type="button" class="btn btn-sm btn-outline-secondary remove-btn">&#10005;</button>
                </div>
            `);
        }
    });

    // Handle the remove uploaded file button click event
    $(document).on("click", ".remove-btn", function () {
        const index = $(this).closest(".file-item").index();
        fileList.splice(index, 1);
        $(this).closest(".file-item").remove();
    });

    // Handle the upload button click event
    $("#upload-btn").click(function (event) {
        event.preventDefault();

        uploadFiles(fileList, $("#current-path").val());
        $("#message-dialog").on("hidden.bs.modal", function () {
            location.reload();
        });
    });
});

// Load functions
function getFileFolder(path) {
    const title = "Load";

    return fetch(
        "http://localhost/api/storage/get_files_folders?path=" +
            encodeURIComponent(path),
        {
            method: "GET",
        }
    )
        .then(async (response) => {
            contentType = response.headers.get("Content-Type");
            if (contentType && contentType.indexOf("application/json") !== -1) {
                response = await response.json();
                if (response.code == 0) return response.data;
                else showMessage(title, response.message);
            }
            return response.text();
        })
        .catch((error) => showMessage(title, error));
}

function loadBreadcrumb(path) {
    var dirs = path.split("/");
    var breadcrumb = $(".breadcrumb");

    for (var i = 0; i < dirs.length; i++) {
        if (i == 0) {
            breadcrumb.append(
                '<li class="breadcrumb-item"><a href="?dir=">Home</a></li>'
            );
        } else if (i == dirs.length - 1)
            breadcrumb.append(
                '<li class="breadcrumb-item active">' + dirs[i] + "</li>"
            );
        else {
            var link = "?dir=";
            for (var j = 1; j <= i; j++) {
                link += "/" + dirs[j];
            }
            breadcrumb.append(
                '<li class="breadcrumb-item"><a href="' +
                    link +
                    '">' +
                    dirs[i] +
                    "</a></li>"
            );
        }
    }
}

function getDisplayContent(file_content) {
    display_file_list = [];
    if (typeof file_content === "string") {
        return file_content;
    } else {
        file_content.forEach((file) => {
            info = file.info;
            display_file_list.push({
                name: info.name,
                type: getFileType(info.extension),
                size: info.size,
                modified_date: info.modified_date,
                icon: getFileIcon(info.extension),
            });
        });
    }
    return display_file_list;
}

function getFileIcon(ext) {
    if (ext == null) return "fas fa-folder";
    if (ext == "pdf") return "fas fa-file-pdf";
    if (ext == "txt") return "fas fa-file-alt";
    if (ext == "mp3") return "fas fa-file-audio";
    if (ext == "docx" || ext == "doc") return "fas fa-file-word";
    if (ext == "zip" || ext == "rar") return "fas fa-file-archive";
    if (ext == "mp4" || ext == "mov" || ext == "mkv")
        return "fas fa-file-video";
    if (ext == "jpg" || ext == "png" || ext == "jpeg")
        return "fas fa-file-image";
    if (ext == "php" || ext == "js" || ext == "html" || ext == "css")
        return "fas fa-file-code";

    return "fas fa-file";
}

function getFileType(ext) {
    if (ext == null) return "Folder";
    if (ext == "pdf") return "PDF";
    if (ext == "txt") return "Text";
    if (ext == "mp3") return "Audio";
    if (ext == "docx" || ext == "doc") return "Word";
    if (ext == "zip" || ext == "rar") return "Archive";
    if (ext == "mp4" || ext == "mov" || ext == "mkv") return "Video";
    if (ext == "jpg" || ext == "png" || ext == "jpeg") return "Image";
    if (ext == "php" || ext == "js" || ext == "html" || ext == "css")
        return "Code";

    return "File";
}

function displayFileContent(display_content) {
    if (typeof display_content === "string") {
        $("#main-content").text(display_content);
    } else {
        // Create the table element
        var table = $("<table>", { class: "table table-hover border" });

        // Create the table header
        var tableHeader = $("<thead>").append(
            $("<tr>").append(
                $("<th>").text("Name"),
                $("<th>").text("Type"),
                $("<th>").text("Size"),
                $("<th>").text("Last modified"),
                $("<th>").text("Actions")
            )
        );

        // Create the table body
        var tableBody = $("<tbody>");

        // Append the header and body to the table
        table.append(tableHeader, tableBody);

        dir = (new URLSearchParams(window.location.search)).get('dir') ?? '';
        display_content.forEach((file) => {
            var path = dir + '/' + file.name;
            var row = $("<tr></tr>");

            var icon = $("<i></i>").addClass(file.icon);
            var name = $('<a></a>').attr('href', '?dir=' + path).text(file.name);
            var nameTd = $("<td></td>").append(icon, name);

            var typeTd = $("<td></td>").text(file.type);

            var sizeTd = $("<td></td>").text(file.size);

            var modifiedDateTd = $("<td></td>").text(file.modified_date);

            var downloadIcon = $("<i></i>")
                .addClass("fa fa-download action")
                .attr("data-filename", file.name);
            var editIcon = $("<i></i>")
                .addClass("fa fa-edit action")
                .attr("data-toggle", "modal")
                .attr("data-target", "#confirm-rename")
                .attr("data-filename", file.name);
            var deleteIcon = $("<i></i>")
                .addClass("fa fa-trash action")
                .attr("data-toggle", "modal")
                .attr("data-target", "#confirm-delete")
                .attr("data-filename", file.name);
            var actionsTd = $("<td></td>").append(
                downloadIcon,
                editIcon,
                deleteIcon
            );

            row.append(nameTd, typeTd, sizeTd, modifiedDateTd, actionsTd);
            tableBody.append(row);
        });

        $("#main-content").html(table);
    }
}

// Upload function
function uploadFiles(fileList, path) {
    const title = "Upload";
    if (fileList.length === 0) {
        showMessage(title, "Please select at least one file.");
        return;
    }

    const formData = new FormData();
    for (let i = 0; i < fileList.length; i++) {
        formData.append("files[]", fileList[i]);
    }
    formData.append("path", path);

    fetch("http://localhost/api/storage/upload_file_folder", {
        method: "POST",
        body: formData,
    })
        .then((response) => {
            if (response.ok) {
                return response.json();
            } else {
                throw new Error("Upload failed.");
            }
        })
        .then((response) => {
            showMessage(title, response.message);
        })
        .catch((error) => {
            showMessage(title, error);
        });
}

// Download function
function downloadFile(filename, path) {
    const title = "Download File";
    fetch("http://localhost/api/storage/download_file_folder", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            path: path + "/" + filename,
        }),
    })
        .then((response) => response.blob())
        .then((data) => {
            if (data instanceof Blob) {
                const objectUrl = URL.createObjectURL(data);
                const link = document.createElement("a");
                link.href = objectUrl;
                link.download = filename;
                link.click();
            } else {
                showMessage(title, data.message);
            }
        })
        .catch((error) => showMessage(title, error));
}

// Create new folder function
function createNewFolder(folder_name, path) {
    $("#new-folder-dialog").modal("hide");
    title = "Create Folder";
    fetch("http://localhost/api/storage/create_folder", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            folder_name: folder_name,
            folder_path: path,
        }),
    })
        .then(function (response) {
            return response.json();
        })
        .then(function (data) {
            showMessage(title, data.message);
        })
        .catch((error) => showMessage(title, error));
}

// Create new file function
function createNewFile(formData) {
    title = "Create File";

    fetch("http://localhost/api/storage/create_file", {
        method: "POST",
        body: formData,
    })
        .then(function (response) {
            return response.json();
        })
        .then(function (data) {
            showMessage(title, data.message);
        })
        .catch((error) => showMessage(title, error));
    $("#new-file-dialog").modal("hide");
}

// Delete file/folder function
function deleteFileFolder(path) {
    $("#confirm-delete").modal("hide");
    title = "Delete";
    fetch("http://localhost/api/storage/delete_file_folder", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            path: path,
        }),
    })
        .then(function (response) {
            return response.json();
        })
        .then(function (data) {
            showMessage(title, data.message);
        })
        .catch((error) => showMessage(title, error));
}

// Rename file/folder function
function renameFileFolder(path, new_name) {
    $("#confirm-rename").modal("hide");
    title = "Rename";

    fetch("http://localhost/api/storage/rename_file_folder", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            path: path,
            new_name: new_name,
        }),
    })
        .then(function (response) {
            return response.json();
        })
        .then(function (data) {
            showMessage(title, data.message);
        })
        .catch((error) => showMessage(title, error));
}

// Show message function
function showMessage(title, message) {
    $("#message-title").text(title);
    $("#message-body").text(message);
    $("#message-dialog").modal("show");
}
