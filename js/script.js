document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('record-form');
    const submitBtn = document.getElementById('submit-btn');
    const cancelBtn = document.getElementById('cancel-btn');
    const formTitle = document.getElementById('form-title');
    const recordIdInput = document.getElementById('record-id');
    const tableBody = document.querySelector('#records-table tbody');
    const themeToggleBtn = document.getElementById('theme-toggle-btn');

    let isEditMode = false;
    let currentId = null;

    // Apply saved theme on load
    if (localStorage.getItem('theme') === 'light') {
        document.body.classList.add('light-mode');
    }

    // Toggle theme
    themeToggleBtn.addEventListener('click', () => {
        document.body.classList.toggle('light-mode');
        const isLight = document.body.classList.contains('light-mode');
        localStorage.setItem('theme', isLight ? 'light' : 'dark');
        themeToggleBtn.textContent = isLight ? 'Dark Mode' : 'Light Mode';
    });

    // Initialize toggle button text
    themeToggleBtn.textContent = document.body.classList.contains('light-mode') ? 'Dark Mode' : 'Light Mode';

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const name = document.getElementById('name').value.trim();
        const kra_pin = document.getElementById('kra_pin').value.trim();
        const category = document.getElementById('category').value.trim();
        const status = document.getElementById('status').value.trim();

        if (!name) {
            alert('Name is required');
            return;
        }

        if (isEditMode) {
            updateRecord(currentId, name, kra_pin, category, status);
        } else {
            createRecord(name, kra_pin, category, status);
        }
    });

    cancelBtn.addEventListener('click', function () {
        resetForm();
    });

    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('edit-btn')) {
            const row = e.target.closest('tr');
            currentId = row.dataset.id;

            document.getElementById('name').value = row.cells[1].textContent;
            document.getElementById('kra_pin').value = row.cells[2].textContent;
            document.getElementById('category').value = row.cells[3].textContent;
            document.getElementById('status').value = row.cells[4].textContent;

            formTitle.textContent = 'Edit Record';
            submitBtn.textContent = 'Update';
            cancelBtn.style.display = 'inline-block';
            isEditMode = true;
            recordIdInput.value = currentId;
        }

        if (e.target.classList.contains('delete-btn')) {
            if (confirm('Are you sure you want to delete this record?')) {
                const id = e.target.dataset.id;
                deleteRecord(id);
            }
        }
    });

    function createRecord(name, kra_pin, category, status) {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'php/create.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.onload = function () {
            if (this.status === 200) {
                const response = JSON.parse(this.responseText);
                if (response.success) {
                    addRowToTable(response.newRecordId, name, kra_pin, category, status);
                    resetForm();
                } else {
                    alert('Error: ' + response.message);
                }
            } else {
                alert('Request failed. Status: ' + this.status);
            }
        };

        xhr.send(`name=${encodeURIComponent(name)}&kra_pin=${encodeURIComponent(kra_pin)}&category=${encodeURIComponent(category)}&status=${encodeURIComponent(status)}`);
    }

    function updateRecord(id, name, kra_pin, category, status) {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'php/update.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.onload = function () {
            if (this.status === 200) {
                const response = JSON.parse(this.responseText);
                if (response.success) {
                    updateRowInTable(id, name, kra_pin, category, status);
                    resetForm();
                } else {
                    alert('Error: ' + response.message);
                }
            } else {
                alert('Request failed. Status: ' + this.status);
            }
        };

        xhr.send(`id=${id}&name=${encodeURIComponent(name)}&kra_pin=${encodeURIComponent(kra_pin)}&category=${encodeURIComponent(category)}&status=${encodeURIComponent(status)}`);
    }

    function deleteRecord(id) {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'php/delete.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.onload = function () {
            if (this.status === 200) {
                const response = JSON.parse(this.responseText);
                if (response.success) {
                    removeRowFromTable(id);
                    resetForm();
                } else {
                    alert('Error: ' + response.message);
                }
            } else {
                alert('Request failed. Status: ' + this.status);
            }
        };

        xhr.send(`id=${id}`);
    }

    function addRowToTable(id, name, kra_pin, category, status) {
        const tr = document.createElement('tr');
        tr.dataset.id = id;
        tr.innerHTML = `
            <td>${id}</td>
            <td>${escapeHtml(name)}</td>
            <td>${escapeHtml(kra_pin)}</td>
            <td>${escapeHtml(category)}</td>
            <td>${escapeHtml(status)}</td>
            <td>
                <button class="edit-btn" data-id="${id}">Edit</button>
                <button class="delete-btn" data-id="${id}">Delete</button>
            </td>
        `;
        tableBody.appendChild(tr);
    }

    function updateRowInTable(id, name, kra_pin, category, status) {
        const row = tableBody.querySelector(`tr[data-id="${id}"]`);
        if (row) {
            row.cells[1].textContent = name;
            row.cells[2].textContent = kra_pin;
            row.cells[3].textContent = category;
            row.cells[4].textContent = status;
        }
    }

    function removeRowFromTable(id) {
        const row = tableBody.querySelector(`tr[data-id="${id}"]`);
        if (row) {
            row.remove();
        }
    }

    function resetForm() {
        form.reset();
        formTitle.textContent = 'Add New Record';
        submitBtn.textContent = 'Submit';
        cancelBtn.style.display = 'none';
        isEditMode = false;
        currentId = null;
        recordIdInput.value = '';
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
});
