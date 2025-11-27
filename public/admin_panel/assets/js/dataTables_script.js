$(document).ready(function () {
    $('#dataTables').DataTable({
        responsive: true,
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search ......",
            lengthMenu: "Show _MENU_ entries",
        }
    });
});