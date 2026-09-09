function confirmAlert(message, callback) {
    swal({
        title: "Are you sure?",
        text: message || "Are you sure you want to continue?",
        icon: "warning",
        buttons: {
            cancel: {
                text: "Cancel",
                visible: true
            },
            confirm: {
                text: "Yes, continue",
                className: "btn-danger"
            }
        },
        dangerMode: true
    }).then(function(confirmed) {
        if (confirmed && typeof callback === "function") {
            callback();
        }
    });

    return false;
}

function showToast(message, type) {
    let container = document.getElementById("toastContainer");

    if (!container) {
        container = document.createElement("div");
        container.id = "toastContainer";
        container.className = "toast-container position-fixed top-0 end-0 p-3";
        container.style.zIndex = "9999";
        document.body.appendChild(container);
    }

    let bgClass = "bg-primary";
    let title = "Information";

    if (type === "success") {
        bgClass = "bg-success";
        title = "Success!";
    }

    if (type === "error") {
        bgClass = "bg-danger";
        title = "Error!";
    }

    if (type === "warning") {
        bgClass = "bg-warning";
        title = "Warning!";
    }

    if (type === "info") {
        bgClass = "bg-info";
        title = "Information";
    }

    let toast = document.createElement("div");

    toast.className = "toast " + bgClass + " text-white border-0 mb-2";
    toast.setAttribute("role", "alert");
    toast.setAttribute("aria-live", "assertive");
    toast.setAttribute("aria-atomic", "true");

    toast.innerHTML = `
        <div class="toast-header ${bgClass} text-white border-0">
            <strong class="me-auto">${title}</strong>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
        </div>
        <div class="toast-body">
            ${message || ""}
        </div>
    `;

    container.appendChild(toast);

    if (typeof bootstrap !== "undefined" && bootstrap.Toast) {
        let bsToast = new bootstrap.Toast(toast, {
            delay: 3000,
            autohide: true
        });

        bsToast.show();

        toast.addEventListener("hidden.bs.toast", function() {
            toast.remove();
        });
    } else {
        console.error("Bootstrap Toast is not loaded.");
        toast.style.display = "block";

        setTimeout(function() {
            toast.remove();
        }, 3000);
    }
}

function successAlert(message) {
    showToast(
        message || "Action completed successfully.",
        "success"
    );
}

function errorAlert(message) {
    showToast(
        message || "Something went wrong.",
        "error"
    );
}

function warningAlert(message) {
    showToast(
        message || "Please check your action.",
        "warning"
    );
}

function infoAlert(message) {
    showToast(
        message || "Information",
        "info"
    );
}

function confirmForm(form, message) {
    swal({
        title: "Are you sure?",
        text: message || "Are you sure you want to continue?",
        icon: "warning",
        buttons: {
            cancel: {
                text: "Cancel",
                visible: true
            },
            confirm: {
                text: "Yes, continue",
                className: "btn-danger"
            }
        },
        dangerMode: true
    }).then(function(confirmed) {
        if (confirmed) {
            form.submit();
        }
    });

    return false;
}

function deleteConfirm(form) {
    return confirmForm(
        form,
        "This role will be moved to trash."
    );
}

function ajaxError(xhr) {
    console.error(xhr);

    let message = "Something went wrong.";

    if (xhr.responseJSON && xhr.responseJSON.message) {
        message = xhr.responseJSON.message;
    }

    errorAlert(message);
}

function changePosition(id, type) {
    let input = document.getElementById("position" + id);

    if (!input) {
        return;
    }

    let currentPosition = parseInt(input.value) || 0;
    let newPosition = currentPosition;

    if (type === "up") {
        newPosition = currentPosition + 1;
    }

    if (type === "down") {
        if (currentPosition <= 0) {
            return;
        }

        newPosition = currentPosition - 1;
    }

    let csrf = document.querySelector(
        'meta[name="csrf-token"]'
    );

    if (!csrf) {
        errorAlert("CSRF token not found.");
        return;
    }

    fetch("/admin/roles/" + id + "/position", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "Accept": "application/json",
            "X-CSRF-TOKEN": csrf.getAttribute("content")
        },
        body: JSON.stringify({
            position: newPosition
        })
    })
    .then(function(response) {
        if (!response.ok) {
            throw new Error(
                "HTTP error " + response.status
            );
        }

        return response.json();
    })
    .then(function(data) {
        if (data.success) {
            input.value = data.position;

            successAlert(
                data.message || "Position updated successfully."
            );

            setTimeout(function() {
                location.reload();
            }, 1000);
        } else {
            errorAlert(
                data.message || "Position update failed."
            );
        }
    })
    .catch(function(error) {
        console.error(error);

        errorAlert(
            "Something went wrong while updating position."
        );
    });
}

function toggleStatus(element, url) {
    let checkbox = element;
    let oldStatus = !checkbox.checked;
    let newStatus = checkbox.checked ? "Active" : "Inactive";

    if (typeof swal !== "function") {
        errorAlert("SweetAlert is not loaded.");
        checkbox.checked = oldStatus;
        return false;
    }

    swal({
        title: "Are you sure?",
        text: "Do you want to change role status to " + newStatus + "?",
        icon: "warning",
        buttons: {
            cancel: {
                text: "Cancel",
                visible: true
            },
            confirm: {
                text: "Yes, change it",
                className: "btn-primary"
            }
        },
        dangerMode: true
    }).then(function(confirmed) {
        if (confirmed) {
            window.location.href = url;
        } else {
            checkbox.checked = oldStatus;
        }
    });

    return false;
}

function openTableJs(id) {
    let currentTable = document.getElementById(
        "openTable" + id
    );

    if (!currentTable) {
        return;
    }

    document.querySelectorAll(
        ".permission-row"
    ).forEach(function(row) {
        row.classList.add("d-none");
    });

    if (currentTable.classList.contains("d-none")) {
        currentTable.classList.remove("d-none");
    }
}

const status = document.getElementById('status');
const statusText = document.getElementById('statusText');

function updateStatusText() {
statusText.textContent = status.checked ? 'ON' : 'OFF';
}

status.addEventListener('change', updateStatusText);
updateStatusText();
