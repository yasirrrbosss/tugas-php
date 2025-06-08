// script.js - Simple JavaScript
document.addEventListener('DOMContentLoaded', function() {

    // Auto-hide messages after 3 seconds
    const messages = document.querySelectorAll('.message');
    messages.forEach(function(message) {
        setTimeout(function() {
            message.style.display = 'none';
        }, 3000);
    });

    // Confirm before delete
    const deleteButtons = document.querySelectorAll('.btn-delete');
    deleteButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this task?')) {
                e.preventDefault();
            }
        });
    });

    // Auto-focus on task input
    const taskInput = document.getElementById('task');
    if (taskInput) {
        taskInput.focus();
    }

    // Enter key to submit form
    if (taskInput) {
        taskInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                document.getElementById('addForm').submit();
            }
        });
    }

    // Real-time character count (optional)
    if (taskInput) {
        taskInput.addEventListener('input', function() {
            const maxLength = 255;
            const currentLength = this.value.length;

            // Simple validation
            if (currentLength > maxLength) {
                this.value = this.value.substring(0, maxLength);
            }
        });
    }

});

// Simple task counter update
function updateStats() {
    const totalTasks = document.querySelectorAll('.task-item').length;
    const completedTasks = document.querySelectorAll('.task-item.completed').length;
    const pendingTasks = totalTasks - completedTasks;

    // Update stats if elements exist
    const totalElement = document.getElementById('total-tasks');
    const completedElement = document.getElementById('completed-tasks');
    const pendingElement = document.getElementById('pending-tasks');

    if (totalElement) totalElement.textContent = totalTasks;
    if (completedElement) completedElement.textContent = completedTasks;
    if (pendingElement) pendingElement.textContent = pendingTasks;
}

// Call updateStats on page load
document.addEventListener('DOMContentLoaded', updateStats);