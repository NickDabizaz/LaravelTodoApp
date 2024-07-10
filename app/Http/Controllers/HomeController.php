<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Todo;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        // If not have session user_id redirect to auth.login
        // check have session the name is user_id or not. MUST SESSION
        if (!session()->has('user_id')) {
            return redirect()->route('login');
        }
        // Get user_id from sesssion then get the todo where user_id is user_id from session
        $todos = Todo::where('user_id', session()->get('user_id'))->get();
        // Return view home.home and send the data is $mode default 1, $todo_id default null, and todos
        return view('home.home', ['mode' => 1, 'todo_id' => null, 'todos' => $todos]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'details' => 'required|string|max:255',
        ]);

        $todo = Todo::create([
            'title' => $request->title,
            'details' => $request->details,
            'user_id' => session()->get('user_id'),
        ]);

        return response()->json($todo);
    }

    public function markAsDone(Todo $todo)
    {
        if (session()->get('user_id') == $todo->user_id) {
            $todo->update(['isDone' => true]);
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
    }


    public function delete(Todo $todo)
    {
        if (session()->get('user_id') == $todo->user_id) {
            $todo->delete();
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
    }



    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'details' => 'required|string|max:255',
        ]);

        $todo = Todo::findOrFail($id);

        if (session()->get('user_id') == $todo->user_id) {
            $todo->update([
                'title' => $request->title,
                'details' => $request->details,
            ]);
        }

        return response()->json($todo);
    }
}
