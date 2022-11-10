<?php

namespace App\Http\Controllers;

use App\Models\User;
use Artesaos\SEOTools\Traits\SEOTools;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use App\Classes\UserAccountingManager;
use App\Http\Requests\PackageExtrasRequest;
use App\Models\Package;
use App\Models\Space;
use App\Models\UserExtra;

class PackageExtrasController extends Controller
{
    use SEOTools;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function index()
    {
        $this->seo()
            ->setTitle(trans('package.title_extras'));

        if (request()->ajax()) {
            if (request()->wantsJson()) {
                $extras = UserExtra::owner()->select(['extras_id', 'extras_type', 'extras_description', 'is_repeating', 'date_end'])->whereDate('date_end', '>', date('Y-m-d H:i:s'))->get();
                $extras->each(function ($item, $key) {
                    $item->date_end_formatted = $item->date_end->format('d.m.Y'); // TODO: I18N
                });

                return response()->json($extras);
            }
        }

        $canAddPlayerConfiguration = has_package_feature(auth()->user()->package, Package::FEATURE_PLAYER_CONFIGURATION);
        $canAddMember = has_package_feature(auth()->user()->package, Package::FEATURE_MEMBERS);
        $canAddStatsExport = has_package_feature(auth()->user()->package, Package::FEATURE_STATISTICS_EXPORT);
        $isTrial = auth()->user()->isInTrial();

        return response()->view('package.extras.index', compact('canAddPlayerConfiguration', 'canAddMember', 'canAddStatsExport', 'isTrial'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     * @throws \Exception
     * @throws \Throwable
     */
    public function store(PackageExtrasRequest $request)
    {
        $validated = $request->validated();
        $count = $validated['amount'];
        $repeating = $validated['repeating'];
        $_type = $validated['type'];
        $type = UserExtra::getTypes()[$validated['type']];

        if ($type === UserExtra::EXTRA_PLAYERCONFIGURATION
            && Gate::denies('canSavePlayerConfigurations')) {
            $msg = ['error' => trans('package.message_failure_extra_store_missing_feature')];
            throw new \Exception($msg['error']);
        }

        $msg = ['error' => trans('package.message_extras_store_error')];
        $uaManager = new UserAccountingManager();

        DB::transaction(function () use ($uaManager, $count, $_type, $type, &$msg, $repeating) {
            $extra = new UserExtra();
            $extra->usr_id = auth()->id();
            $extra->date_created = Carbon::now();
            $extra->date_start = Carbon::now();
            $extra->date_end = Carbon::now()->addMonth();
            $extra->is_repeating = $repeating;
            $extra->extras_count = $count;
            $description = trans('package.text_package_extra_description_'.$_type,
                ['piece' => UserExtra::getPieces()[$type] * $count]);
            $extra->extras_description = $description;
            $extra->extras_type = $type;
            $extra->saveOrFail();

            $extra->user->funds -= $extra->extras_count;
            $extra->user->save();

            $uaManager->trackOrder($extra);

            $msg = ['success' => trans('package.message_extras_store_success'), 'text' => trans('package.message_plus_extras_store_success', ['extra' => $description])];
        });

        if (request()->ajax()) {
            if (request()->wantsJson()) {
                return response()->json($msg);
            }
            return response()->json($msg);
        }

        return redirect()->route('extras.index')->with($msg);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\UserExtra  $userExtra
     * @return \Illuminate\Http\Response
     */
    public function show(UserExtra $userExtra)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\UserExtra  $userExtra
     * @return \Illuminate\Http\Response
     */
    public function edit(UserExtra $userExtra)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\UserExtra  $userExtra
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     * @throws \Exception
     */
    public function update($id)
    {
        $extra = UserExtra::owner()->findOrFail($id);
        $msg = ['success' => trans('package.message_success_extra_cancelled', ['name' => $extra->extras_description])];

        // Feed
        if ($extra->extras_type === UserExtra::EXTRA_FEED) {
            $aCount = get_package_feature_feeds(auth()->user()->package, auth()->user());
            if ($aCount['total'] - $extra->extras_count < $aCount['used']) {
                $surplus = $aCount['used'] - ($aCount['total'] - $extra->extras_count);
                $msg = ['error' => trans('package.message_failure_extra_cancelled_too_many_feeds', ['used' => $aCount['used']])
                    . PHP_EOL . trans('package.message_plus_failure_extra_cancelled_too_many_feeds', ['surplus' => $surplus])];
                throw new \Exception($msg['error']);
            }
        }
        // Player configurations
        if ($extra->extras_type === UserExtra::EXTRA_PLAYERCONFIGURATION) {
            $aCount = get_player_configuration_count(auth()->user());
            if ($aCount['total'] - $extra->extras_count < $aCount['used']) {
                $surplus = $aCount['used'] - ($aCount['total'] - $extra->extras_count);
                $msg = ['error' => trans('package.message_failure_extra_cancelled_too_many_player_configurations', ['used' => $aCount['used']])
                    . PHP_EOL . trans('package.message_plus_failure_extra_cancelled_too_many_player_configurations', ['surplus' => $surplus])];
                throw new \Exception($msg['error']);
            }
        }

        // Contributors
        if ($extra->extras_type === UserExtra::EXTRA_MEMBER) {
            $aCount = get_package_feature_members(auth()->user()->package, /** User */ auth()->user());

            if ($aCount['total'] - $extra->extras_count < $aCount['used']) {
                $surplus = $aCount['used'] - ($aCount['total'] - $extra->extras_count);
                $msg = ['error' => trans('package.message_failure_extra_cancelled_too_many_member_configurations', ['used' => $aCount['used']])
                    . PHP_EOL . trans('package.message_plus_failure_extra_cancelled_too_many_member_configurations', ['surplus' => $surplus])];
                throw new \Exception($msg['error']);
            }
        }

        if (!$extra || !$extra->update(['is_repeating' => false])) {
            $msg = ['error' => trans('package.message_failure_extra_cancelled')];
        }

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json($msg);
        }

        return redirect()->route('extras.index')->with($msg);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return void
     */
    public function destroy($id)
    {
    }
}
