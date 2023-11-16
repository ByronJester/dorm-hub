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

        return redirect()->route('help.edit')->with('success', 'Added FAQs successfully.');
    }
    
    public function editPage(){
        $helps = Help::all();
        return Inertia::render('Admin/EditPage/EditHelp', [
            'helps' => $helps,
        ]);

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

        $help = Help::first($id);
        $help->update($validatedData);

        return redirect()>route('help.edit')->with('success', 'Updated FAQs successfully.');
    }

    public function destroy($id)
    {
        $help = Help::first($id);
        $help->delete();

        return redirect()->back();
    }
}
