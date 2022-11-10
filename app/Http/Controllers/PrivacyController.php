<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Av;
//use App\Models\Tom;
use App\Models\UserDpa;
use App\Models\UserDpaAttribute;
use Illuminate\Support\Facades\Log;
use Webpatser\Countries\Countries;
use PDF;

class PrivacyController extends Controller
{
    const SET_PDF_WARNINGS = false;

    use SoftDeletes;

    public function dpa()
    {
        \SEO::setTitle(trans('privacy.title_dpa'));

        $aDpa = UserDpa::where('usr_id', auth()->id())->orderBy('id', 'desc')->get();
        $countries = Countries::select(['iso_3166_2', 'name'])->get();
        $av = Av::orderBy('id', 'desc')->first();

        return view('av', ['dpas' => $aDpa, 'countries' => $countries, 'av' => $av ?? new Av()]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'dpa_data' => 'required|array|min:1|in:meta,contact,content',
            'dpa_concerned' => 'required|array|min:1',
            'dpa_confirmation' => 'required|in:on',
            'av_id' => 'required|exists:avs,id',
            'first_name' => 'required',
            'last_name' => 'required',
        ]);
        $dpa = new UserDpa($request->except(['_token', 'dpa_data', 'dpa_concerned', 'dpa_confirmation']));
        $dpa->usr_id = auth()->id();

        $msg = ['error' => 'Der Auftragsverarbeitungsvertrag konnte nicht angelegt werden.'];
        $aAttr = [];

        foreach ($request->dpa_data as $val) {
            $aAttr[] = [
                'data' => $val,
                'type' => UserDpaAttribute::TYPE_DATA,
            ];
        }
        foreach ($request->dpa_concerned as $val) {
            $aAttr[] = [
                'data' => $val,
                'type' => UserDpaAttribute::TYPE_CONCERNED,
            ];
        }

        DB::beginTransaction();
        try {
            if ($dpa->saveOrFail()) {
                $dpa->attributes()->createMany($aAttr);
                $msg = ['success' => 'Der AV-Vertrag wurde erfolgreich angelegt.'];
            }
            DB::commit();
        } catch (\Exception $e) {
            $msg = ['error' => 'Der AV-Vertrag konnte nicht angelegt werden.'];
            $username = auth()->user()->username;
            Log::error("ERROR: User: $username: {$msg['error']}: " . $e->getMessage());
            DB::rollBack();
        }

        return redirect()->route('dpa')->with($msg);
    }

    public function delete($id)
    {
        $dpa = UserDpa::findOrFail($id);

        if (!$dpa->isOwner()) {
            if (request()->ajax() || request()->wantsJson()) {
                return response('Unauthorized.', 401);
            }
            abort(401);
        }

        $msg = ['error' => 'Der AV-Vertrag konnte nicht gelöscht werden.'];

        if ($dpa->delete()) {
            $msg = ['success' => 'Der AV-Vertrag wurde erfolgreich gelöscht.'];
        }

        return redirect()->route('dpa')->with($msg);
    }

    public function download($id)
    {
        $dpa = UserDpa::findOrFail($id);

        if (!$dpa->isOwner()) {
            if (request()->ajax() || request()->wantsJson()) {
                return response('Unauthorized.', 401);
            }
            abort(401);
        }

        $av = Av::findOrFail($dpa->av_id);
        $pdf = PDF::loadView('pdf.av' . $av->id, ['dpa' => $dpa]);

        return $pdf
            ->setPaper('a4')
            ->setWarnings(self::SET_PDF_WARNINGS)
            ->download('podcaster-auftragsverarbeitungsvertrag_v' . $av->id . '.pdf');
    }

    public function av()
    {
        $av = Av::orderBy('id', 'desc')->first();

        if (!$av) {
            abort(404);
        }

        $pdf = PDF::loadView('pdf.av' . $av->id, []);

        return $pdf
            ->setPaper('a4')
            ->setWarnings(self::SET_PDF_WARNINGS)
            ->download('podcaster-auftragsverarbeitungsvertrag-vorlage_v' . $av->id . '.pdf');
    }

}
