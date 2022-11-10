<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\UserOauth;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     * @hideFromAPIDocumentation
     */
    public function index()
    {
        return response()->json(auth()->user()->approvals);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @hideFromAPIDocumentation
     */
    public function destroy($id)
    {
        $uo = UserOauth::owner()->findOrFail($id);
        $msg = ['success' => trans('approvals.success_deleted_approval')];

        if (!$uo->delete()) {
            $msg = ['error' => trans('approvals.error_deleting_approval_failed')];
        }

        return response()->json($msg);
    }
}
