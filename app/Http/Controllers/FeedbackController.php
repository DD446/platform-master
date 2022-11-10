<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create()
    {
        \SEO::setTitle(trans('feedback.title'));

        $hideNav = true;
        $type = \request()->type;
        $entity = \request()->entity;

        return view('feedback.create', compact('hideNav', 'type', 'entity'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $types = Feedback::types();

        $validated = $this->validate($request, [
            'comment' => 'required',
            'type' => 'required|in:' . implode(',', array_keys($types)),
            'screenshot' => 'nullable|image|mimes:jpeg,bmp,png,gif|max:15000',
            'entity' => 'nullable',
        ], [], [
            'comment' => trans('feedback.label_comment_field'),
            'screenshot' => trans('feedback.validation_screenshot'),
        ]);

        $feedback = new Feedback();
        $feedback->fill($validated);
        // User delete feedback
        if ($feedback->type == 11) {
            $feedback->user_id = $feedback->entity;
        } else {
            $feedback->user_id = auth()->user()->id;
        }

        if ($request->hasFile('screenshot')) {
            $feedback->addMedia($request->file('screenshot'))->toMediaCollection('screenshot');
        }

        $msg = ['success' => trans('feedback.success_feedback_saved')];

        if (!$feedback->save()) {
            $msg = ['error' => trans('errors.feedback_not_saved')];
        }

        if ($request->wantsJson()) {
            return response()->json($msg);
        }

        if ($feedback->type == 11) {
            return response()->redirectToRoute('home')->with('status', trans('user.message_success_package_deleted_feedback'));
        }

        return redirect()->back()->with($msg);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Feedback  $feedback
     * @return \Illuminate\Http\Response
     */
    public function show(Feedback $feedback)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Feedback  $feedback
     * @return \Illuminate\Http\Response
     */
    public function edit(Feedback $feedback)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Feedback  $feedback
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Feedback $feedback)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Feedback  $feedback
     * @return \Illuminate\Http\Response
     */
    public function destroy(Feedback $feedback)
    {
        //
    }
}
