<?php

namespace App\Http\Controllers;

use App\Models\UserOauth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ApprovalController extends Controller
{
    public function index()
    {
        \SEO::setTitle(trans('approvals.page_title'));

        $hasAuphonicFeature = Gate::forUser(auth()->user())->allows('hasAuphonicFeature');

        return view('user.approval.index', compact('hasAuphonicFeature'));
    }
}
