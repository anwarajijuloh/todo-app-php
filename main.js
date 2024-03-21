function confirmDelete(id) {
    if (confirm("Are you sure you want to delete this todo?")) {
        document.querySelector('input[name="id"]').value = id;
        document.querySelector('input[name="deleteTodo"]').click();
    }
}

function cancelEdit() {
    // Redirect to current page to cancel edit
    window.location.href = window.location.pathname;
}