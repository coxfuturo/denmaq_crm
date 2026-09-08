<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="DANMAQ ERP professional admin dashboard template">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>DANMAQ ERP | DASHBOARD</title>
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/bootstrap-icons/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>
<body>
    <div class="admin-shell">
        <div class="sidebar-backdrop" data-sidebar-close></div>

        @include('admin.layout.sidebar')

        <div class="admin-main">
            @include('admin.layout.header')

            <main class="dashboard-content">
                @yield('content')
            </main>

            @include('admin.layout.footer')
        </div>
    </div>

    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert@2.1.2/dist/sweetalert.min.js"></script>

    <script>
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

        function successAlert(message) {
            swal({
                title: "Success!",
                text: message || "Action completed successfully.",
                icon: "success",
                button: "OK"
            });
        }

        function errorAlert(message) {
            swal({
                title: "Error!",
                text: message || "Something went wrong.",
                icon: "error",
                button: "OK"
            });
        }

        function warningAlert(message) {
            swal({
                title: "Warning!",
                text: message || "Please check your action.",
                icon: "warning",
                button: "OK"
            });
        }

        function infoAlert(message) {
            swal({
                title: "Information",
                text: message || "",
                icon: "info",
                button: "OK"
            });
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
            return confirmForm(form, "This role will be moved to trash.");
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

            let csrf = document.querySelector('meta[name="csrf-token"]');

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
                    throw new Error("HTTP error " + response.status);
                }

                return response.json();
            })
            .then(function(data) {
                if (data.success) {
                    input.value = data.position;
                    location.reload();
                } else {
                    errorAlert(data.message || "Position update failed.");
                }
            })
            .catch(function(error) {
                console.error(error);
                errorAlert("Something went wrong while updating position.");
            });
        }

        function toggleStatus(element, url) {
            let checkbox = element;
            let oldStatus = !checkbox.checked;
            let newStatus = checkbox.checked ? "Active" : "Inactive";

            if (typeof swal !== "function") {
                alert("SweetAlert is not loaded.");
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
            let currentTable = document.getElementById("openTable" + id);

            if (!currentTable) {
                return;
            }

            document.querySelectorAll(".permission-row").forEach(function(row) {
                row.classList.add("d-none");
            });

            if (currentTable.classList.contains("d-none")) {
                currentTable.classList.remove("d-none");
            }
        }
    </script>
</body>
</html>
