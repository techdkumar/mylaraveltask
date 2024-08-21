<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Manager</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        /* Style for the input field with gray background */
        #taskInput {
            background-color: #d3d3d3; /* Light gray background */
            color: #333; /* Darker text color for better readability */
            border: 1px solid #ccc; /* Border for the input field */
            border-radius: 4px; /* Rounded corners */
        }

        /* Custom table styles */
        .table-custom {
            background-color: #f9f9f9; /* Light gray background for the table */
            border-radius: 8px; /* Rounded corners */
            overflow: hidden; /* Hide overflow */
            margin-top: 20px; /* Add margin above the table */
        }

        .table-custom thead {
            background-color: #007bff; /* Blue header background */
            color: white; /* White text color */
        }

        .table-custom tbody tr:nth-of-type(odd) {
            background-color: #f2f2f2; /* Light gray for odd rows */
        }

        .table-custom tbody tr:nth-of-type(even) {
            background-color: #e9ecef; /* Slightly darker gray for even rows */
        }

        /* Button styles */
        #addTaskButton {
            background-color: #007bff; /* Blue background */
            color: white; /* White text color */
            border: none; /* Remove border */
            border-radius: 4px; /* Rounded corners */
            padding: 10px; /* Padding inside the button */
        }

        #showAllButton {
            background-color: #17a2b8; /* Info color background */
            color: white; /* White text color */
            border: none; /* Remove border */
            border-radius: 4px; /* Rounded corners */
            padding: 10px; /* Padding inside the button */
        }

        /* Style for completed tasks */
        .completed {
            text-decoration: line-through; /* Strike-through for completed tasks */
            color: grey; /* Gray text color for completed tasks */
        }

        /* Hide completed tasks by default */
        .hidden {
            display: none;
        }

        /* Style for the delete button image */
        .delete-icon {
            width: 16px; /* Set width */
            height: 16px; /* Set height */
            cursor: pointer; /* Cursor change on hover */
        }
		
		
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1>Task Manager</h1>

        <div class="row mb-3">
            <div class="col-md-8">
                <input type="text" id="taskInput" placeholder="Enter task name" class="form-control">
            </div>
            <div class="col-md-4">
                <button id="addTaskButton" class="btn w-100">Add Task</button>
            </div>
        </div>

        <table class="table table-striped table-custom">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Task Name</th>
                    <th>Completed</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="taskList">
                <!-- Tasks will be dynamically appended here -->
            </tbody>
        </table>

        <button id="showAllButton" class="btn mt-3">Show All Tasks</button>
		
    </div>

    <script>
        let showAll = false;

        // Fetch and display tasks
        function loadTasks() {
            $.get('/tasks', function(tasks) {
                $('#taskList').empty();
                tasks.forEach((task, index) => {
                    let hiddenClass = (!showAll && task.is_completed) ? 'hidden' : '';
                    let taskRow = `
                        <tr id="task-${task.id}" class="${task.is_completed ? 'completed' : ''} ${hiddenClass}">
                            <td>${index + 1}</td>
                            <td>${task.name}</td>
                            <td>
                                <input type="checkbox" class="toggle-complete" data-id="${task.id}" ${task.is_completed ? 'checked' : ''}>
                            </td>
                            <td>
                                <img src="{{ asset('images/delete-icon.png') }}" class="delete-icon delete-task" data-id="${task.id}" alt="Delete">
                            </td>
                        </tr>
                    `;
                    $('#taskList').append(taskRow);
                });
            });
        }

        // Add task
        $('#addTaskButton').click(function() {
            const taskName = $('#taskInput').val().trim();
            if (taskName) {
                $.post('/tasks', {name: taskName}, function(task) {
                    loadTasks();
                    $('#taskInput').val('');  
                }).fail(function(error) {
                    alert(error.responseJSON.message);  
                });
            }
        });

        // Toggle task completion
$(document).on('change', '.toggle-complete', function() {
    const taskId = $(this).data('id');
    const isCompleted = $(this).is(':checked'); 

    $.ajax({
        url: `/tasks/${taskId}`,  
        method: 'PATCH',  // Using PATCH method
        data: { is_completed: isCompleted }, 
        success: function() {
            loadTasks();  // Reload the task list
			console.log('Task ID:', taskId, 'Is Completed:', isCompleted);
        },
        error: function(xhr) {
            alert('Error updating task: ' + xhr.responseJSON.message);
        }
    });
});

        // Delete task with confirmation
        $(document).on('click', '.delete-task', function() {
            const taskId = $(this).data('id');
            if (confirm('Are you sure to delete this task?')) {
                $.ajax({
                    url: `/tasks/${taskId}`,
                    method: 'DELETE',
                    success: function() {
                        loadTasks();
                    }
                });
            }
        });

        // Show all tasks (toggle between showing all and hiding completed)
        $('#showAllButton').click(function() {
            showAll = !showAll;  // Toggle the showAll state
            $(this).text(showAll ? 'Hide Completed Tasks' : 'Show All Tasks');
            loadTasks();  // Reload the task list to reflect the new state
        });

        // Initial load
        loadTasks();

        // Set up CSRF token for AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
		

		
		
    </script>
</body>
</html>
