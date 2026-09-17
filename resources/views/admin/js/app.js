function confirmAlert(message, callback) {
    if (typeof swal !== "function") {
        console.error("SweetAlert is not loaded.");

        if (typeof callback === "function") {
            callback();
        }

        return false;
    }

    swal({
        title: "Are you sure?",
        text: message || "Are you sure you want to continue?",
        icon: "warning",
        buttons: {
            cancel: {
                text: "Cancel",
                visible: true,
                closeModal: true
            },
            confirm: {
                text: "Yes, continue",
                className: "btn-danger",
                closeModal: true
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

function logoutConfirm() {
    return confirmAlert(
        "Are you sure you want to logout?",
        function() {
            let form = document.getElementById("logoutForm");

            if (form) {
                form.submit();
            } else {
                console.error("Logout form not found.");
            }
        }
        );
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
    } else if (type === "error") {
        bgClass = "bg-danger";
        title = "Error!";
    } else if (type === "warning") {
        bgClass = "bg-warning";
        title = "Warning!";
    } else if (type === "info") {
        bgClass = "bg-info";
        title = "Information";
    }

    let toast = document.createElement("div");

    toast.className = "toast " + bgClass + " text-white border-0 mb-2";
    toast.setAttribute("role", "alert");
    toast.setAttribute("aria-live", "assertive");
    toast.setAttribute("aria-atomic", "true");

    toast.innerHTML =
    '<div class="toast-header ' + bgClass + ' text-white border-0">' +
    '<strong class="me-auto">' + escapeJsHtml(title) + '</strong>' +
    '<button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>' +
    '</div>' +
    '<div class="toast-body">' +
    escapeJsHtml(message || "") +
    '</div>';

    container.appendChild(toast);

    if (
        typeof bootstrap !== "undefined" &&
        bootstrap.Toast
        ) {
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

function escapeJsHtml(value) {
    return String(value)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;");
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
    if (!form) {
        console.error("Form not found.");
        return false;
    }

    if (typeof swal !== "function") {
        console.error("SweetAlert is not loaded.");
        form.submit();
        return false;
    }

    swal({
        title: "Are you sure?",
        text: message || "Are you sure you want to continue?",
        icon: "warning",
        buttons: {
            cancel: {
                text: "Cancel",
                visible: true,
                closeModal: true
            },
            confirm: {
                text: "Yes, continue",
                className: "btn-danger",
                closeModal: true
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

function deleteConfirm(form, message) {
    return confirmForm(
        form,
        message || "This record will be moved to trash."
        );
}

function forceDeleteConfirm(form, message) {
    return confirmForm(
        form,
        message || "This record will be permanently deleted. This action cannot be undone."
        );
}

function restoreConfirm(form, message) {
    return confirmForm(
        form,
        message || "This record will be restored from trash."
        );
}

function ajaxError(xhr) {
    console.error(xhr);

    let message = "Something went wrong.";

    if (
        xhr &&
        xhr.responseJSON &&
        xhr.responseJSON.message
        ) {
        message = xhr.responseJSON.message;
} else if (
    xhr &&
    xhr.responseText
    ) {
    try {
        let response = JSON.parse(xhr.responseText);

        if (response.message) {
            message = response.message;
        }
    } catch (error) {
        console.error(error);
    }
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
    } else if (type === "down") {
        if (currentPosition <= 0) {
            return;
        }

        newPosition = currentPosition - 1;
    } else {
        return;
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

function toggleStatus(element, url, message) {
    let checkbox = element;

    if (!checkbox) {
        return false;
    }

    let oldStatus = !checkbox.checked;
    let newStatus = checkbox.checked ? "Active" : "Inactive";

    if (typeof swal !== "function") {
        console.error("SweetAlert is not loaded.");
        checkbox.checked = oldStatus;
        return false;
    }

    swal({
        title: "Are you sure?",
        text: message || "Do you want to change status to " + newStatus + "?",
        icon: "warning",
        buttons: {
            cancel: {
                text: "Cancel",
                visible: true,
                closeModal: true
            },
            confirm: {
                text: "Yes, change it",
                className: "btn-primary",
                closeModal: true
            }
        },
        dangerMode: false
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

    document.addEventListener("DOMContentLoaded", function() {
        const status = document.getElementById("status");
        const statusText = document.getElementById("statusText");

        function updateStatusText() {
            if (!status || !statusText) {
                return;
            }

            statusText.textContent = status.checked
            ? "ON"
            : "OFF";
        }

        if (status && statusText) {
            status.addEventListener(
                "change",
                updateStatusText
                );

            updateStatusText();
        }
    });