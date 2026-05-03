<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\crud\CustomRequest;

class CRUDController extends Controller
{
    // Display a listing of the resource [cite: 666]
    public function index()
    {
        return response()->view('crud.index', [
            'User' => User::orderBy('name', 'asc')->get(),
        ]);
    }

    // Show the form for creating a new resource [cite: 805]
    public function create()
    {
        return response()->view('crud.dataform');
    }

    // Store a newly created resource in storage [cite: 818]
    public function store(CustomRequest $request)
    {
        //  dd(get_class($request));
        $validated = $request->validated();
        $create = User::create($validated);

        if ($create) {
            session()->flash('notif.success', 'New data created');
            return redirect()->route('crud.index');
        }
        return abort(500);
    }

    // Display the specified resource [cite: 998]
    public function show(string $id)
    {
        return response()->view('crud.details', [
            'User' => User::findOrFail($id),
        ]);
    }

    // Show the form for editing the specified resource [cite: 1134]
    public function edit(string $id)
    {
        return response()->view('crud.dataform', [
            'User' => User::findOrFail($id),
        ]);
    }

    // Update the specified resource in storage [cite: 1158]
    public function update(CustomRequest $request, string $id)
    {
        $User = User::findOrFail($id);
        $validated = $request->validated();
        $update = $User->update($validated);

        if($update) {
            session()->flash('notif.success', 'Data successfully updated');
            return redirect()->route('crud.index');
        }
        return abort(500);
    }

    // Remove the specified resource from storage [cite: 1234]
    public function destroy(string $id)
    {
        $User = User::findOrFail($id);
        $delete = $User->delete();

        if($delete) {
            session()->flash('notif.success', 'Data successfully deleted');
            return redirect()->route('crud.index');
        }
        return abort(500);
    }
}