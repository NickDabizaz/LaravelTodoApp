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
    <div class="max-w-md mx-auto bg-white p-6 rounded-lg shadow-lg">
        <h1 class="text-2xl font-bold mb-4">Todo App</h1>

        <button id="addTodoButton" class="bg-blue-500 text-white px-4 py-2 rounded mb-4">Add Todo</button>

        <div id="addTodoModal"
            class="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center">
            <div class="bg-white p-4 rounded shadow-md w-1/3">
                <form id="addTodoForm">
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
        <div id="todoList">
            @foreach ($todos as $todo)
                <div class="border border-gray-300 p-4 rounded mb-2 flex justify-between items-center"
                    id="todo-{{ $todo->id }}">
                    <span>{{ $todo->title }}</span>
                    <div class="flex space-x-2">
                        @if (!$todo->isDone)
                            <form method="POST" action="{{ route('todos.done', $todo->id) }}">
                                @csrf
                                <button type="submit" class="text-green-500">
                                    <i class="fas fa-check"></i>
                                </button>
                            </form>
                            <button class="text-blue-500 editTodoButton" data-id="{{ $todo->id }}"
                                data-title="{{ $todo->title }}" data-details="{{ $todo->details }}">
                                <i class="fas fa-pencil-alt"></i>
                            </button>
                        @else
                            <span class="text-green-500 font-semibold">(Completed)</span>
                        @endif
                        <form method="POST" action="{{ route('todos.delete', $todo->id) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded mt-4">Logout</button>
        </form>
    </div>

    <div id="editTodoModal"
        class="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center">
        <div class="bg-white p-4 rounded shadow-md w-1/3">
            <form id="editTodoForm">
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

    <script>
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
                            '<div class="border border-gray-300 p-4 rounded mb-2 flex justify-between items-center" id="todo-' +
                            response.id + '">' +
                            '<span>' + response.title + '</span>' +
                            '<div class="flex space-x-2">' +
                            '<form method="POST" class="markAsDoneForm" data-id="' +
                            response.id + '">' +
                            '@csrf' +
                            '<button type="submit" class="text-green-500"><i class="fas fa-check"></i></button>' +
                            '</form>' +
                            '<button class="text-blue-500 editTodoButton" data-id="' +
                            response.id + '" data-title="' + response.title +
                            '" data-details="' + response.details + '">' +
                            '<i class="fas fa-pencil-alt"></i>' +
                            '</button>' +
                            '<form method="POST" class="deleteTodoForm" data-id="' +
                            response.id + '">' +
                            '@csrf' +
                            '@method('DELETE')' +
                            '<button type="submit" class="text-red-500"><i class="fas fa-trash"></i></button>' +
                            '</form>' +
                            '</div>' +
                            '</div>'
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
                        $('#todo-' + id).find('span').text(response.title);
                        $('#todo-' + id).find('.editTodoButton').data('title', response.title)
                            .data('details', response.details);
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
                        $('#todo-' + id).find('span').append(
                            ' <span class="text-green-500 font-semibold">(Completed)</span>'
                        );
                        $('#todo-' + id).find('.markAsDoneForm').remove();
                        $('#todo-' + id).find('.editTodoButton').remove();
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
                        console.log('Success response:',
                        response); // Log untuk melihat respons sukses
                        if (response.success) {
                            $('#todo-' + id).remove();
                        }
                    },
                    error: function(response) {
                        console.log('Error response:',
                        response); // Log untuk melihat respons error
                        alert('Error: ' + response.responseJSON.message);
                    }
                });
            });
        });
    </script>
</body>

</html>
