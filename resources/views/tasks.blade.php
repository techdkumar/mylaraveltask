<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Manager</title>
	
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	 <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <h1>Task Manager</h1>
    <input type="text" id="task" placeholder="Enter a new task">
    <button id="addTask">Add Task</button>
    <button id="showAllTasks">Show All Tasks</button>
    <ul id="taskList"></ul>

    <script>
        $(document).ready(function() {
            function loadTasks() {
                $.get('/tasks', function(tasks) {
                    $('#taskList').empty();
                    tasks.forEach(task => {
                        $('#taskList').append(`
                            <li data-id="${task.id}">
                                <input type="checkbox" class="completeTask" ${task.completed ? 'checked' : ''}>
                                ${task.task}
                                <button class="deleteTask">Delete</button>
                            </li>
                        `);
                    });
                });
            }

            $('#addTask').click(function() {
                const task = $('#task').val();
                $.post('/tasks', { task: task, _token: '{{ csrf_token() }}' }, function(newTask) {
                    loadTasks();
                    $('#task').val('');
                }).fail(function() {
                    alert('Task already exists');
                });
            });

            $(document).on('change', '.completeTask', function() {
                const taskId = $(this).closest('li').data('id');
                $.ajax({
                    url: '/tasks/' + taskId,
                    type: 'PUT',
                    success: function() {
                        loadTasks();
                    }
                });
            });

            $(document).on('click', '.deleteTask', function() {
                const taskId = $(this).closest('li').data('id');
                if (confirm('Are you sure to delete this task?')) {
                    $.ajax({
                        url: '/tasks/' + taskId,
                        type: 'DELETE',
                        success: function() {
                            loadTasks();
                        }
                    });
                }
            });

            $('#showAllTasks').click(function() {
                loadTasks();
            });

            loadTasks(); // Initial load
        });
		
		   // Set up CSRF token for AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
</body>
</html>