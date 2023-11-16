<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Models\Help;
use Inertia\Inertia;

class HelpController extends Controller
{
    // public function index()
    // {
    //     $faqs = Help::all();
    //     return view('table_help.index', compact('faqs'));
    // }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'question' => 'required',
            'answer' => 'required',
        ]);

        Help::create($validatedData);

        return redirect()->back();
    }
    
    public function editPage(){
        return Inertia::render('Admin/EditPage/EditHelp', []);

    }

    public function edit($id)
    {
        $help = Help::findOrFail($id);
        return view('table_help.edit', compact('help'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'question' => 'required',
            'answer' => 'required',
        ]);

        $help = Help::findOrFail($id);
        $help->update($validatedData);

        return redirect()->back();
    }

    public function destroy($id)
    {
        $help = Help::first($id);
        $help->delete();

        return redirect()->back();
    }
}
