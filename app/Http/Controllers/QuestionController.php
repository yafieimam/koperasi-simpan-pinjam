<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Question;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $questions = Question::get();
        return view('question.question-list', compact('questions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('question.question-create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
		$question = new Question();
		$question->name = $request->name;
		$question->description = $request->description;
		$question->save();

        session()->flash('success', trans('response-message.success.create',['object'=>'Tanya Jawab']));
        return redirect('qna-data');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\GeneralSetting  $generalSetting
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\GeneralSetting  $generalSetting
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $question = Question::findOrFail($id);
        return view('question.question-edit', compact('question'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\GeneralSetting  $generalSetting
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
		$question = Question::findOrFail($id);
		$question->name = $request->name;
		$question->description = $request->description;
		$question->update();

        session()->flash('success', trans('response-message.success.update',['object'=>'Tanya Jawab']));
        return redirect('qna-data');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\GeneralSetting  $generalSetting
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (Question::destroy($id)) {
            $data = 'Success';
         }else{
             $data = 'Failed';
         }
        session()->flash('success', trans('response-message.success.delete',['object'=>'Tanya Jawab']));
        return redirect('qna-data');
        // return response()->json($data);
	}

	public function datatable($questions)
    {
        if($questions == 'all')
        {
            $questions = Question::all();
		}
        return \DataTables::of($questions)
            ->editColumn('name', function($question){
                return $question->name;
            })

            ->addColumn('action', function($question){
                $deleteUrl = url('qna-data').'/'.$question->id;
                $editUrl = url('qna-data').'/'.$question->id.'/edit';
                $btnEdit = '';
                $btnDelete = '';
                if(auth()->user()->can('edit.master.qna')){
                    $btnEdit = '<a class="btn btn-primary" href="'.$editUrl.'"><i class="fa fa-edit"></i></a>';
                }
                if(auth()->user()->can('delete.master.qna')){
                    $btnDelete = '<form method="POST" action="'.$deleteUrl.'" class="form-inline">'.csrf_field().method_field('DELETE').'<button class="btn btn-danger" type="submit"><i class="fa fa-trash"></i></button> </form>';
                }
				return $btnEdit.' '.$btnDelete;
            })->make(true);
	}
}
