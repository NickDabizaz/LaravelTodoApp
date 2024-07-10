<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todo App</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="bg-gray-100 p-6">
    <div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow-lg">
        <h1 class="text-2xl font-bold mb-4">Todo App</h1>

        <button id="addTodoButton" class="bg-blue-500 text-white px-4 py-2 rounded mb-4">Add Todo</button>

        <div id="addTodoModal"
            class="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center">
            <div class="bg-white p-4 rounded shadow-md w-1/3">
                <form id="addTodoForm" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                        <input type="text" id="title" name="title" class="border p-2 w-full rounded">
                    </div>
                    <div class="mb-4">
                        <label for="details" class="block text-sm font-medium text-gray-700">Details</label>
                        <textarea id="details" name="details" class="border p-2 w-full rounded"></textarea>
                    </div>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Submit</button>
                </form>
            </div>
        </div>

        <h2 class="text-xl font-semibold mb-2">List of Todos:</h2>
        <table class="table-auto w-full">
            <thead>
                <tr>
                    <th class="px-4 py-2">No</th>
                    <th class="px-4 py-2">Title</th>
                    <th class="px-4 py-2">Done</th>
                    <th class="px-4 py-2">Edit</th>
                    <th class="px-4 py-2">Delete</th>
                    <th class="px-4 py-2">Details</th>
                </tr>
            </thead>
            <tbody id="todoList">
                @foreach ($todos as $index => $todo)
                    <tr id="todo-{{ $todo->id }}">
                        <td class="border px-4 py-2">{{ $index + 1 }}</td>
                        <td class="border px-4 py-2">{{ $todo->title }}</td>
                        <td class="border px-4 py-2">
                            @if (!$todo->isDone)
                                <form method="POST" class="markAsDoneForm" data-id="{{ $todo->id }}">
                                    @csrf
                                    <button type="submit" class="text-green-500"><i class="fas fa-check"></i></button>
                                </form>
                            @else
                                <span class="text-green-500 font-semibold">(Completed)</span>
                            @endif
                        </td>
                        <td class="border px-4 py-2">
                            <button class="text-blue-500 editTodoButton" data-id="{{ $todo->id }}"
                                data-title="{{ $todo->title }}" data-details="{{ $todo->details }}">
                                <i class="fas fa-pencil-alt"></i>
                            </button>
                        </td>
                        <td class="border px-4 py-2">
                            <form method="POST" class="deleteTodoForm" data-id="{{ $todo->id }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                        <td class="border px-4 py-2">
                            <a href="#" class="text-blue-500 seeDetails" data-details="{{ $todo->details }}">See
                                Details</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded mt-4">Logout</button>
        </form>
    </div>

    <div id="editTodoModal"
        class="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center">
        <div class="bg-white p-4 rounded shadow-md w-1/3">
            <form id="editTodoForm" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_todo_id" name="todo_id">
                <div class="mb-4">
                    <label for="edit_title" class="block text-sm font-medium text-gray-700">Title</label>
                    <input type="text" id="edit_title" name="title" class="border p-2 w-full rounded">
                </div>
                <div class="mb-4">
                    <label for="edit_details" class="block text-sm font-medium text-gray-700">Details</label>
                    <textarea id="edit_details" name="details" class="border p-2 w-full rounded"></textarea>
                </div>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Submit</button>
            </form>
        </div>
    </div>

    <div id="detailsModal"
        class="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center">
        <div class="bg-white p-4 rounded shadow-md w-1/3">
            <h2 class="text-xl font-semibold mb-2">Todo Details</h2>
            <p id="detailsContent"></p>
            <button id="closeDetailsButton" class="bg-blue-500 text-white px-4 py-2 rounded mt-4">Close</button>
        </div>
    </div>

    <script>
        function updateTodoNumbers() {
            $('#todoList tr').each(function(index) {
                $(this).find('td:first').text(index + 1);
            });
        }

        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#addTodoButton').click(function() {
                $('#addTodoModal').removeClass('hidden');
            });

            $('#addTodoModal').on('click', function(event) {
                if ($(event.target).is('#addTodoModal')) {
                    $(this).addClass('hidden');
                }
            });

            $('#editTodoModal').on('click', function(event) {
                if ($(event.target).is('#editTodoModal')) {
                    $(this).addClass('hidden');
                }
            });

            $('#detailsModal').on('click', function(event) {
                if ($(event.target).is('#detailsModal') || $(event.target).is('#closeDetailsButton')) {
                    $(this).addClass('hidden');
                }
            });

            $('#addTodoForm').submit(function(e) {
                e.preventDefault();
                var title = $('#title').val();
                var details = $('#details').val();

                $.ajax({
                    type: 'POST',
                    url: '{{ route('todos.store') }}',
                    data: {
                        title: title,
                        details: details
                    },
                    success: function(response) {
                        $('#addTodoModal').addClass('hidden');
                        $('#title').val('');
                        $('#details').val('');
                        $('#todoList').append(
                            '<tr id="todo-' + response.id + '">' +
                            '<td class="border px-4 py-2">' + ($('#todoList tr').length +
                            1) + '</td>' +
                            '<td class="border px-4 py-2">' + response.title + '</td>' +
                            '<td class="border px-4 py-2">' +
                            '<form method="POST" class="markAsDoneForm" data-id="' +
                            response.id + '">' +
                            '@csrf' +
                            '<button type="submit" class="text-green-500"><i class="fas fa-check"></i></button>' +
                            '</form>' +
                            '</td>' +
                            '<td class="border px-4 py-2">' +
                            '<button class="text-blue-500 editTodoButton" data-id="' +
                            response.id + '" data-title="' + response.title +
                            '" data-details="' + response.details + '">' +
                            '<i class="fas fa-pencil-alt"></i>' +
                            '</button>' +
                            '</td>' +
                            '<td class="border px-4 py-2">' +
                            '<form method="POST" class="deleteTodoForm" data-id="' +
                            response.id + '">' +
                            '@csrf' +
                            '@method('DELETE')' +
                            '<button type="submit" class="text-red-500"><i class="fas fa-trash"></i></button>' +
                            '</form>' +
                            '</td>' +
                            '<td class="border px-4 py-2">' +
                            '<a href="#" class="text-blue-500 seeDetails" data-details="' +
                            response.details + '">See Details</a>' +
                            '</td>' +
                            '</tr>'
                        );
                    },
                    error: function(response) {
                        alert('Error: ' + response.responseJSON.message);
                    }
                });
            });

            $(document).on('click', '.editTodoButton', function() {
                var id = $(this).data('id');
                var title = $(this).data('title');
                var details = $(this).data('details');

                $('#edit_todo_id').val(id);
                $('#edit_title').val(title);
                $('#edit_details').val(details);
                $('#editTodoForm').attr('action', '/todos/' + id);
                $('#editTodoModal').removeClass('hidden');
            });

            $('#editTodoForm').submit(function(e) {
                e.preventDefault();
                var id = $('#edit_todo_id').val();
                var title = $('#edit_title').val();
                var details = $('#edit_details').val();

                $.ajax({
                    type: 'PUT',
                    url: '/todos/' + id,
                    data: {
                        title: title,
                        details: details
                    },
                    success: function(response) {
                        $('#editTodoModal').addClass('hidden');
                        $('#todo-' + id).find('td:nth-child(2)').text(response.title);
                        $('#todo-' + id).find('.editTodoButton').data('title', response.title)
                            .data('details', response.details);
                        $('#todo-' + id).find('.seeDetails').data('details', response.details);
                    },
                    error: function(response) {
                        alert('Error: ' + response.responseJSON.message);
                    }
                });
            });

            $(document).on('submit', '.markAsDoneForm', function(e) {
                e.preventDefault();
                var id = $(this).data('id');

                $.ajax({
                    type: 'POST',
                    url: '/todos/' + id + '/done',
                    success: function(response) {
                        if (response.success) {
                            $('#todo-' + id).find('td:nth-child(3)').html(
                                '<span class="text-green-500 font-semibold">(Completed)</span>'
                                );
                            $('#todo-' + id).find('.markAsDoneForm').remove();
                            $('#todo-' + id).find('.editTodoButton').remove();
                        }
                    },
                    error: function(response) {
                        alert('Error: ' + response.responseJSON.message);
                    }
                });
            });

            $(document).on('submit', '.deleteTodoForm', function(e) {
                e.preventDefault();
                var id = $(this).data('id');

                $.ajax({
                    type: 'DELETE',
                    url: '/todos/' + id,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#todo-' + id).remove();
                            updateTodoNumbers();
                        }
                    },
                    error: function(response) {
                        alert('Error: ' + response.responseJSON.message);
                    }
                });
            });

            $(document).on('click', '.seeDetails', function(e) {
                e.preventDefault();
                var details = $(this).data('details');
                $('#detailsContent').text(details);
                $('#detailsModal').removeClass('hidden');
            });
        });

        function updateTodoNumbers() {
            $('#todoList tr').each(function(index) {
                $(this).find('td:first').text(index + 1);
            });
        }
    </script>
</body>

</html>
