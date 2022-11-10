<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use App\Http\Requests\PlayerConfigRequest;
use App\Models\Package;
use App\Models\PlayerConfig;
use App\Models\UserExtra;

class PlayerConfigController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index()
    {
        \SEO::setTitle(trans('player.title_configurator'));

        if (request()->ajax() || request()->wantsJson()) {
            $configs = PlayerConfig::owner()->get()->toArray();

            return response()->json($configs);
        }
        $canEmbed = Gate::forUser(auth()->user())->allows('viewPlayerConfigurator');
        $canCreatePlayerConfigurations = Gate::forUser(auth()->user())->allows('canSavePlayerConfigurations');
        $canUseCustomPlayerStyles = Gate::forUser(auth()->user())->allows('canUseCustomStylesForPlayer');
        $amountPlayerConfigurations = $this->getNumberConfigsAllowedInPackage();

        return view('player.config.index', compact('canCreatePlayerConfigurations', 'canUseCustomPlayerStyles', 'canEmbed', 'amountPlayerConfigurations'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  PlayerConfigRequest  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function store(PlayerConfigRequest $request)
    {
        if (Gate::forUser(auth()->user())->denies('viewPlayerConfigurator')) {
            abort(403, trans('player.message_error_feature_access_denied'));
        }
        $msg = ['success' => trans('player.message_success_configuration_saved', ['name' => request('title')])];

        $attributes = $request->all();
        $attributes['uuid'] = Str::uuid()->toString();
        $attributes['user_id'] = auth()->user()->usr_id;
        $pc = new PlayerConfig($attributes);

        if (!$pc->save()) {
            $msg = ['error' => trans('player.message_error_configuration_save_failed', ['name' => $pc->title])];
        } else {
            $msg['config'] = $pc;
        }

        if (request()->ajax()) {
            if (request()->wantsJson()) {
                return response()->json($msg);
            }
            return response()->json($msg);
        }
        return redirect()->route('player.config.index')->with($msg);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function update(PlayerConfigRequest $request, $id)
    {
        if (Gate::forUser(auth()->user())->denies('canSavePlayerConfigurations')) {
            abort(403, trans('player.message_error_feature_access_denied'));
        }

        $validated = $request->validated();

        $msg = ['success' => trans('player.message_success_configuration_updated', ['name' => request('title')])];

        $pc = PlayerConfig::findOrFail($id);

        if (!$pc->update($validated)) {
            $msg = ['error' => trans('player.message_error_configuration_update_failed', ['name' => $pc->title])];
        }

        if (request()->ajax()) {
            if (request()->wantsJson()) {
                return response()->json($msg);
            }
            return response()->json($msg);
        }
        return redirect()->route('player.config.index')->with($msg);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $msg = ['success' => trans('player.success_player_configuration_deleted')];

        $config = PlayerConfig::owner()->find($id);

        if (!$config || !$config->delete()) {
            $msg = ['error' => trans('player.failure_player_configuration_deleted')];
        }

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json($msg);
        }

        return redirect()->route('player.config.index')->with($msg);
    }

    public function copy($id)
    {
        $msg = ['error' => trans('player.failure_player_configuration_copied')];

        $config = PlayerConfig::owner()->findOrFail($id);
        $copy = $config->replicate();
        $copy->uuid = Str::uuid()->toString();
        $copy->title .= ' (' . trans('player.text_copied_configuration_hint') . ')';

        if ($copy->save()) {
            $msg = [
                'success' => trans('player.success_player_configuration_copied', ['title' => $copy->title]),
                'copy' => $copy,
            ];
        }
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json($msg);
        }

        return redirect()->route('player.config.index')->with($msg);
    }

    protected function getNumberConfigsAllowedInPackage()
    {
        return get_player_configuration_count(auth()->user())['total'];
    }
}
