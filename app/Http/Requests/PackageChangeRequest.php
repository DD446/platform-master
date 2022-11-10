<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Package;
use App\Rules\CanUseTrackerRule;
use App\Rules\HasEnoughBlogs;
use App\Rules\HasEnoughCustomDomains;
use App\Rules\HasEnoughFeedsRule;
use App\Rules\HasEnoughFundsRule;
use App\Rules\HasEnoughMembersRule;
use App\Rules\HasEnoughPlayerConfigurationsRule;
use App\Rules\HasEnoughSubdomains;
use App\Rules\HasEnoughSubdomainsPremium;
use App\Rules\HasProtectedFeedsRule;
use App\Rules\HasEnoughSpaceRule;
use App\Rules\HasScheduledPostsRule;
use App\Scopes\IsVisibleScope;

class PackageChangeRequest extends FormRequest
{
    /**
     * PackageChangeRequest constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // During trial users can switch to whatever package they want
        if (auth()->user()->isInTrial()) {
            return [
                'id' => [
                    'required',
                    'numeric',
                ]
            ];
        }

        $packageId = request()->id;
        /** @var Package $newPackage */
        $newPackage = Package::with('features')
            ->withoutGlobalScope(IsVisibleScope::class)
            ->where('package_id', '=', $packageId)
            ->firstOrFail();

        return [
            'id' => [
                'required',
                'numeric',
                new HasEnoughFeedsRule($newPackage),
                new HasEnoughSpaceRule($newPackage),
                new HasProtectedFeedsRule($newPackage),
                new HasScheduledPostsRule($newPackage),
                new HasEnoughCustomDomains($newPackage),
                new HasEnoughBlogs($newPackage),
                new HasEnoughSubdomains($newPackage),
                new HasEnoughSubdomainsPremium($newPackage),
                new HasEnoughPlayerConfigurationsRule($newPackage),
                new HasEnoughFundsRule($newPackage),
                new HasEnoughMembersRule($newPackage),
                new CanUseTrackerRule($newPackage),
            ],
            //'fid' => 'required|numeric',
        ];
    }

    public function attributes()
    {
        return [
            'id' => trans('package.text_package_name'),
        ];
    }

    public function messages()
    {
        return [
            'id.required' => trans('package.validation_error_id_required'),
        ];
    }
}
