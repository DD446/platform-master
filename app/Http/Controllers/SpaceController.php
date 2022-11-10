<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Space;
use App\Models\UserUpload;

class SpaceController extends Controller
{
    public function index()
    {
        \SEO::setTitle(trans('spaces.page_title'));
        \SEO::metatags()->addMeta(['robots' => 'noindex']);

        $accountingTimes = get_user_accounting_times(auth()->user()->id);
        $availableSpacePackage = get_size_readable(Space::available()->owner()->whereType(Space::TYPE_REGULAR)->sum('space_available'));
        $availableSpaceExtras = get_size_readable(Space::available()->owner()->whereType(Space::TYPE_EXTRA)->sum('space_available'));
        $userSpace = get_user_space(auth()->user());
        $space = $userSpace['total'];
        $spacePackage = $userSpace['included'];
        $spaceExtras = $userSpace['extra'];
        $availableSpace = $userSpace['available'];
        $userUploads = UserUpload::owner()->with('space')->orderByDesc('created_at')->simplePaginate(15);

        return view('spaces.index', compact('accountingTimes', 'space', 'spacePackage', 'spaceExtras', 'availableSpace', 'availableSpacePackage', 'availableSpaceExtras', 'userUploads'));
    }
}
