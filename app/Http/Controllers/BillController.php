<?php

namespace App\Http\Controllers;

use App\Classes\UserPaymentManager;
use App\Models\User;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Classes\PaymentLegacy;
use App\Classes\TaxCalculator;
use App\Models\UserBillingContact;
use App\Models\UserPayment;
use Spatie\Browsershot\Browsershot;

class BillController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index()
    {
        \SEO::setTitle(trans('bills.page_title_index'));

        $bills = UserPayment::owner()->whereNotNull('bill_id')->orderByDesc('date_created')->paginate(10);
        $contact = UserBillingContact::firstOrNew(['user_id' => auth()->id()]);
        $canDownloadBills = $contact->last_name && $contact->street && $contact->housenumber && $contact->post_code && $contact->city && $contact->country;
        $countries = \Countrylist::getList('de');

        return view('bills.index', compact('bills', 'canDownloadBills', 'countries'));
    }

    public function show(string $id)
    {
        $payment = UserPayment::owner()->with('payer')->whereBillId($id)->firstOrFail();
        (new UserPaymentManager)->prepareForBill($payment);

        //$hideNav = true;
        //return view('bills.invoice', compact('payment', 'hideNav'));
        return view('bills.show', compact('payment'));
    }

    public function download(string $id)
    {
        if (in_array(auth()->user()->role_id, [User::ROLE_ADMIN, User::ROLE_SUPPORTER])) {
            $payment = UserPayment::with('payer')->whereBillId($id)->firstOrFail();
        } else {
            $payment = UserPayment::owner()->with('payer')->whereBillId($id)->firstOrFail();
        }

        $file = (new UserPaymentManager())->saveBill($payment);
        $filename = basename($file);

        return response()->download($file, $filename);
    }

    public function backups()
    {
        if (Gate::forUser(auth()->user())->denies('viewBillsInBackup')) {
            abort(403);
        }

        $files = Storage::disk('bills')->files('backups');

        return view('bills.backups', compact('files'));

        //return response()->download($file['path'], $file['name']/*, $headers, 'attachment'*/);

    }

    public function downloadBackup()
    {
        if (Gate::forUser(auth()->user())->denies('viewBillsInBackup')) {
            abort(403);
        }

        $backupDir = 'backups';
        $backups = Storage::disk('bills');
        $fs = $backups->getDriver();
        $files = $backups->files($backupDir);
        $file = $files[request('id')];
        $filename = Str::after($file, '/');
        $metaData = $fs->getMetadata($file);
        $stream = $fs->readStream($file);

        if (ob_get_level()) ob_end_clean();

        return response()->stream(
            function () use ($stream) {
                fpassthru($stream);
            },
            200,
            [
                'Content-Type' => $metaData['type'],
                'Content-disposition' => 'attachment; filename="' . $filename . '"',
            ]);
    }
}
