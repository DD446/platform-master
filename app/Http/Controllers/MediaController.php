<?php

namespace App\Http\Controllers;

use Alfrasc\MatomoTracker\Facades\LaravelMatomoTracker;
use App\Classes\MediaManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;
use Pion\Laravel\ChunkUpload\Handler\AbstractHandler;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;
use App\Events\FileSavedEvent;
use App\Events\UpdateShowGuid;
use App\Models\Space;
use App\Models\User;
use App\Rules\StorageSpaceAvailable;
use App\Models\Package;

class MediaController extends Controller
{
    /**
     * List files
     *
     * Get list of uploaded files.
     *
     * @group Media
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
     */
    public function index()
    {
        if (Gate::forUser(auth()->user())->denies('viewMedia')) {
            abort(403);
        }

        if (request()->wantsJson()) {
            $files = auth()
                ->user()
                ->getFiles(
                    request('sortBy', 'name'),
                    request('sortDesc', false),
                    request('filter', null),
                    request('currentPage', 1),
                    request('perPage', 10)
                );
            // Remove "path" attribute. It´s not supposed to be exposed
            $files['items'] = array_map(
                function($file) {
                    unset($file->path);
                    return $file;
                },
                $files['items']
            );

            return response()->json($files);
        }

        \SEO::setTitle(trans('mediamanager.page_title'));

        return view('media.index');
    }

    /**
     *
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create()
    {
        \SEO::setTitle(trans('mediamanager.page_title_upload'));

        // Hat der Benutzer negatives Guthaben?
        $uploadBlocked = false;
        $funds = auth()->user()->funds;
        if ($funds < 0) {
            // Ist der aktuelle Abrechnungszeitraum nicht mehr bezahlt?

            $packageCost = Package::where('package_id', '=', auth()->user()->package_id)->value('monthly_cost');

            // Sind der Paketpreis + letzte gebuchte Betrag (Paketpreis, Zusatzoption) kleiner 0?
            // Dann war das Guthaben bereits nicht mehr ausreichend!
            if ($funds + $packageCost < 0) {
                $uploadBlocked = true;
            }

            // @TODO: Präziser überprüfen, ob der Upload geblockt werden kann/sollte
        }

        $spaceAvailable = Space::available()->owner()->sum('space_available');

        return view('media.create', ['hideNav' => true, 'uploadBlocked' => $uploadBlocked, 'spaceAvailable' => $spaceAvailable]);
    }

    /**
     * Upload
     *
     * Handles the file upload.
     *
     * @bodyParam file file required The media file to upload.
     *
     * @param  Request  $request
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws UploadMissingFileException
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Exception
     */
    public function storeChunks(Request $request)
    {
        $this->validate($request, [
            'file' => ['file', 'required', new StorageSpaceAvailable],
        ], [], [
            'file' => 'Datei',
        ]);

        // create the file receiver
        $receiver = new FileReceiver("file", $request, HandlerFactory::classFromRequest($request));

        // check if the upload is success, throw exception or return response you need
        if ($receiver->isUploaded() === false) {
            throw new UploadMissingFileException();
        }

        // receive the file
        $save = $receiver->receive();

        // check if the upload has finished (in chunk mode it will send smaller files)
        if ($save->isFinished()) {
            // save the file and return any response you need, current example uses `move` function. If you are
            // not using move, you need to manually delete the file by unlink($save->getFile()->getPathname())
            $mediaManager = new MediaManager(auth()->user());
            $aRes = $mediaManager->saveFile($save->getFile());
            @unlink($save->getFile()->getPathname());
            unset($aRes['file']['path']);

            return response()->json($aRes)->setStatusCode($aRes['statusCode'], $aRes['statusText']);
        }

        // we are in chunk mode, lets send the current progress
        /** @var AbstractHandler $handler */
        $handler = $save->handler();

        return response()->json([
            "done" => $handler->getPercentageDone(),
            'status' => true
        ]);
    }

    /**
     * Upload file
     *
     * Stores a file in the media manager.
     *
     * @group Media
     * @bodyParam file file required The media file to upload.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'file' => ['file', 'required', new StorageSpaceAvailable()],
        ], [], [
            'file' => 'Datei',
        ]);

        $mediaManager = new MediaManager(auth()->user());
        $aRes = $mediaManager->saveFile($request->file('file'));

        unset($aRes['file']['path']);

        if ($request->wantsJson()) {
            return response()->json($aRes)->setStatusCode($aRes['statusCode'], $aRes['statusText']);
        }

        return redirect()->back()->with([
            'success' => $aRes['statusText']
        ]);
    }

    /**
     * Get file
     *
     * Gets details for a media file.
     *
     * @group Media
     *
     * @param  int  $id file id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function show($id)
    {
        $file = auth()->user()->getFile($id);

        if (!$file) {
            abort(404);
        }

        if (request()->wantsJson()) {

            $oInfo          = new \finfo(\FILEINFO_NONE |  \FILEINFO_CONTINUE |  \FILEINFO_PRESERVE_ATIME);
            if ($oInfo) {
                $file['info'] = Str::before($oInfo->file($file['path']), '\\'); // Details on file
            }

            $oMime          = new \finfo(\FILEINFO_MIME_TYPE |  \FILEINFO_CONTINUE |  \FILEINFO_PRESERVE_ATIME);
            if ($oMime) {
                $file['mime'] = Str::before($oMime->file($file['path']), '\\');
            }

            unset($file['path']);

            return response()->json($file);
        }

        return response()->file($file['path']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function edit($id)
    {
        $file = auth()->user()->getFile($id);

        if (!$file) {
            abort(404);
        }

        $hideNav = true;

        return view('media.edit', compact('id', 'file', 'hideNav'));
    }

    /**
     * Replace file
     *
     * Upload a new file to replace an existing one.
     * This automatically renews the GUID for episodes
     * where this file is used and refreshes the feed.
     *
     * @group Media
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     * @throws \Exception
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'file' => 'file|required',
        ], [], [
            'file' => 'Datei',
        ]);

        /** @var User $user */
        $user = auth()->user();
        $file = $user->getFile($id);

        if (!$file) {
            abort(404);
        }

        $dir = $id;
        $group = File::basename(File::dirname($file['path']));
        // File is in a subfolder
        if ($group != $id) {
            $dir .= DIRECTORY_SEPARATOR . $group;
        }
        $uploadPath = get_user_media_path($user->username) . DIRECTORY_SEPARATOR . $dir;
        $path = $request->file->storeAs($uploadPath, $file['name']);
        $msg = ['success' => trans('mediamanager.message_success_file_replacement', ['name' => $file['name']])];

        event(new UpdateShowGuid($user->username, $id));

        if (!$path || $path != $uploadPath . DIRECTORY_SEPARATOR . $file['name']) {
            $msg = ['error' => "Die Datei konnte nicht ersetzt werden."];
            return response()->json($msg)->setStatusCode(500, trans('errors.file_upload'));
        }

        $aRes = [
            'message' => $msg,
            'id' => $id,
            'file' => get_file($user->username, $id),
        ];
        return response()->json($aRes);
    }

    /**
     * Delete file
     *
     * Remove a file from the media manager.
     *
     * @group Media
     *
     * @param  int  $id required
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy($id)
    {
        /** @var User $user */
        $user = auth()->user();

        try {
            if (!$user->deleteFile($id)) {
                throw new \Exception(trans('mediamanager.failure_delete_media'));
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], $e->getCode());
        }

        return response()->json([
            'message' => trans('mediamanager.success_delete_media')
        ]);
    }

    /**
     * Copy file
     *
     * Creates a copy of an existing media file with a unique name
     *
     * @group Media
     * @urlParam media_id int required ID of a media file. Example: 123456798
     * @bodyParam label string required Name of new file. Example: "datei_version2.mp3"
     * @bodyParam category string required Group name for new file. (Default: _default_). Example: "Audios"
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function copy(Request $request)
    {
        $this->validate($request, [
                'media_id' => ['required', 'integer', new StorageSpaceAvailable],
                'label' => 'required',
                'category' => 'required',
            ],
            [],
            [
                'media_id' => trans('mediamanager.validation_media_id'),
                'label' => trans('mediamanager.validation_label'),
                'category' => trans('mediamanager.validation_category'),
            ]
        );

        /** @var User $user */
        $user = auth()->user();
        $file = $user->copyFile($request->media_id, $request->label, $request->category);

        return response()->json([
            'message' => trans('mediamanager.success_copy_media', $file)
        ]);
    }

    /**
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Exception
     */
    public function rename(Request $request)
    {
        $this->validate($request, [
                'id' => 'required|integer',
                'label' => 'required',
            ],
            [],
            [
                'label' => trans('mediamanager.validation_label'),
            ]
        );

        /** @var User $user */
        $user = auth()->user();

        if (!$user->renameFile($request->id, $request->label, $request->category)) {
            throw new \Exception(trans('mediamanager.failure_rename_media'));
        }

        return response()->json([
            'message' => trans('mediamanager.success_rename_media', ['label' => $request->label])
        ]);
    }

    public function download($id)
    {
        set_time_limit(0);
        ini_set('memory_limit', '512M');

        $user = auth()->user();
        $file = $user->getFile($id);

/*        if ($file['byte'] > 10000000) { // > 10 MB
            $fs = Storage::disk('mediafiles')->getDriver();
            $fileName = get_user_path($user->username) . DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR . $file['name'];
            $metaData = $fs->getMetadata($fileName);
            $stream = $fs->readStream($fileName);

            if (ob_get_level()) ob_end_clean();

            return response()->stream(
                function () use ($stream) {
                    fpassthru($stream);
                },
                200,
                [
                    'Content-Type' => $metaData['type'],
                    'Content-disposition' => 'attachment; filename="' . $file['name'] . '"',
                ]);
        }*/
        LaravelMatomoTracker::queueDownload(get_direct_uri($user->username, 'download', $file['name']));

        return response()->download($file['path'], $file['name']/*, $headers, 'attachment'*/);
    }

    function offerUrlAsDownload($filePath)
    {
        set_time_limit(0);
        ini_set('memory_limit', '512M');

        if (!empty($filePath)) {
                $fileInfo = pathinfo($filePath);
                $fileName = $fileInfo['basename'];
                $fileExtension = $fileInfo['extension'];
                $defaultContentType = "application/octet-stream";
                $mimeTypes = $this->getMimeTypes();

                // to find and use specific content type, check out this IANA page : http://www.iana.org/assignments/media-types/media-types.xhtml
                if (array_key_exists($fileExtension, $mimeTypes)) {
                    $contentType = $mimeTypes[$fileExtension];
                } else {
                    $contentType = $defaultContentType;
                }

                if (file_exists($filePath)) {
                    $size = filesize($filePath);
                    $offset = 0;
                    $length = $size;
                    //HEADERS FOR PARTIAL DOWNLOAD FACILITY BEGINS
                    if (isset($_SERVER['HTTP_RANGE'])) {
                        preg_match('/bytes=(\d+)-(\d+)?/', $_SERVER['HTTP_RANGE'], $matches);
                        $offset = intval($matches[1]);
                        $length = intval($matches[2]) - $offset;
                        $fhandle = fopen($filePath, 'r');
                        fseek($fhandle,
                            $offset); // seek to the requested offset, this is 0 if it's not a partial content request
                        $data = fread($fhandle, $length);
                        fclose($fhandle);
                        header('HTTP/1.1 206 Partial Content');
                        header('Content-Range: bytes '.$offset.'-'.($offset + $length).'/'.$size);
                    }
                    //HEADERS FOR PARTIAL DOWNLOAD FACILITY BEGINS
                    //USUAL HEADERS FOR DOWNLOAD
                    header("Content-Disposition: attachment;filename=".$fileName);
                    header('Content-Type: '.$contentType);
                    header("Accept-Ranges: bytes");
                    header("Pragma: public");
                    header("Expires: -1");
                    header("Cache-Control: no-cache");
                    header("Cache-Control: public, must-revalidate, post-check=0, pre-check=0");
                    header("Content-Length: ".filesize($filePath));

                    $chunksize = 8 * (1024 * 1024); //8MB (highest possible fread length)

                    if ($size > $chunksize) {
                        $handle = fopen($_FILES["file"]["tmp_name"], 'rb');
                        $buffer = '';
                        while (!feof($handle) && (connection_status() === CONNECTION_NORMAL)) {
                            $buffer = fread($handle, $chunksize);
                            print $buffer;
                            ob_flush();
                            flush();
                        }
                        if (connection_status() !== CONNECTION_NORMAL) {
                            throw new \Exception();
                        }
                        fclose($handle);
                    } else {
                        ob_clean();
                        flush();
                        readfile($filePath);
                    }
                }
            }
    }

    /* Function to get correct MIME type for download */
    private function getMimeTypes()
    {
        /* Just add any required MIME type if you are going to download something not listed here.*/
        return [
            "323" => "text/h323",
            "acx" => "application/internet-property-stream",
            "ai" => "application/postscript",
            "aif" => "audio/x-aiff",
            "aifc" => "audio/x-aiff",
            "aiff" => "audio/x-aiff",
            "asf" => "video/x-ms-asf",
            "asr" => "video/x-ms-asf",
            "asx" => "video/x-ms-asf",
            "au" => "audio/basic",
            "avi" => "video/x-msvideo",
            "axs" => "application/olescript",
            "bas" => "text/plain",
            "bcpio" => "application/x-bcpio",
            "bin" => "application/octet-stream",
            "bmp" => "image/bmp",
            "c" => "text/plain",
            "cat" => "application/vnd.ms-pkiseccat",
            "cdf" => "application/x-cdf",
            "cer" => "application/x-x509-ca-cert",
            "class" => "application/octet-stream",
            "clp" => "application/x-msclip",
            "cmx" => "image/x-cmx",
            "cod" => "image/cis-cod",
            "cpio" => "application/x-cpio",
            "crd" => "application/x-mscardfile",
            "crl" => "application/pkix-crl",
            "crt" => "application/x-x509-ca-cert",
            "csh" => "application/x-csh",
            "css" => "text/css",
            "dcr" => "application/x-director",
            "der" => "application/x-x509-ca-cert",
            "dir" => "application/x-director",
            "dll" => "application/x-msdownload",
            "dms" => "application/octet-stream",
            "doc" => "application/msword",
            "dot" => "application/msword",
            "dvi" => "application/x-dvi",
            "dxr" => "application/x-director",
            "eps" => "application/postscript",
            "etx" => "text/x-setext",
            "evy" => "application/envoy",
            "exe" => "application/octet-stream",
            "fif" => "application/fractals",
            "flr" => "x-world/x-vrml",
            "gif" => "image/gif",
            "gtar" => "application/x-gtar",
            "gz" => "application/x-gzip",
            "h" => "text/plain",
            "hdf" => "application/x-hdf",
            "hlp" => "application/winhlp",
            "hqx" => "application/mac-binhex40",
            "hta" => "application/hta",
            "htc" => "text/x-component",
            "htm" => "text/html",
            "html" => "text/html",
            "htt" => "text/webviewhtml",
            "ico" => "image/x-icon",
            "ief" => "image/ief",
            "iii" => "application/x-iphone",
            "ins" => "application/x-internet-signup",
            "isp" => "application/x-internet-signup",
            "jfif" => "image/pipeg",
            "jpe" => "image/jpeg",
            "jpeg" => "image/jpeg",
            "jpg" => "image/jpeg",
            "js" => "application/x-javascript",
            "latex" => "application/x-latex",
            "lha" => "application/octet-stream",
            "lsf" => "video/x-la-asf",
            "lsx" => "video/x-la-asf",
            "lzh" => "application/octet-stream",
            "m13" => "application/x-msmediaview",
            "m14" => "application/x-msmediaview",
            "m3u" => "audio/x-mpegurl",
            "man" => "application/x-troff-man",
            "mdb" => "application/x-msaccess",
            "me" => "application/x-troff-me",
            "mht" => "message/rfc822",
            "mhtml" => "message/rfc822",
            "mid" => "audio/mid",
            "mny" => "application/x-msmoney",
            "mov" => "video/quicktime",
            "movie" => "video/x-sgi-movie",
            "mp2" => "video/mpeg",
            "mp3" => "audio/mpeg",
            "mpa" => "video/mpeg",
            "mpe" => "video/mpeg",
            "mpeg" => "video/mpeg",
            "mpg" => "video/mpeg",
            "mpp" => "application/vnd.ms-project",
            "mpv2" => "video/mpeg",
            "ms" => "application/x-troff-ms",
            "mvb" => "application/x-msmediaview",
            "nws" => "message/rfc822",
            "oda" => "application/oda",
            "p10" => "application/pkcs10",
            "p12" => "application/x-pkcs12",
            "p7b" => "application/x-pkcs7-certificates",
            "p7c" => "application/x-pkcs7-mime",
            "p7m" => "application/x-pkcs7-mime",
            "p7r" => "application/x-pkcs7-certreqresp",
            "p7s" => "application/x-pkcs7-signature",
            "pbm" => "image/x-portable-bitmap",
            "pdf" => "application/pdf",
            "pfx" => "application/x-pkcs12",
            "pgm" => "image/x-portable-graymap",
            "pko" => "application/ynd.ms-pkipko",
            "pma" => "application/x-perfmon",
            "pmc" => "application/x-perfmon",
            "pml" => "application/x-perfmon",
            "pmr" => "application/x-perfmon",
            "pmw" => "application/x-perfmon",
            "pnm" => "image/x-portable-anymap",
            "pot" => "application/vnd.ms-powerpoint",
            "ppm" => "image/x-portable-pixmap",
            "pps" => "application/vnd.ms-powerpoint",
            "ppt" => "application/vnd.ms-powerpoint",
            "prf" => "application/pics-rules",
            "ps" => "application/postscript",
            "pub" => "application/x-mspublisher",
            "qt" => "video/quicktime",
            "ra" => "audio/x-pn-realaudio",
            "ram" => "audio/x-pn-realaudio",
            "ras" => "image/x-cmu-raster",
            "rgb" => "image/x-rgb",
            "rmi" => "audio/mid",
            "roff" => "application/x-troff",
            "rtf" => "application/rtf",
            "rtx" => "text/richtext",
            "scd" => "application/x-msschedule",
            "sct" => "text/scriptlet",
            "setpay" => "application/set-payment-initiation",
            "setreg" => "application/set-registration-initiation",
            "sh" => "application/x-sh",
            "shar" => "application/x-shar",
            "sit" => "application/x-stuffit",
            "snd" => "audio/basic",
            "spc" => "application/x-pkcs7-certificates",
            "spl" => "application/futuresplash",
            "src" => "application/x-wais-source",
            "sst" => "application/vnd.ms-pkicertstore",
            "stl" => "application/vnd.ms-pkistl",
            "stm" => "text/html",
            "svg" => "image/svg+xml",
            "sv4cpio" => "application/x-sv4cpio",
            "sv4crc" => "application/x-sv4crc",
            "t" => "application/x-troff",
            "tar" => "application/x-tar",
            "tcl" => "application/x-tcl",
            "tex" => "application/x-tex",
            "texi" => "application/x-texinfo",
            "texinfo" => "application/x-texinfo",
            "tgz" => "application/x-compressed",
            "tif" => "image/tiff",
            "tiff" => "image/tiff",
            "tr" => "application/x-troff",
            "trm" => "application/x-msterminal",
            "tsv" => "text/tab-separated-values",
            "txt" => "text/plain",
            "uls" => "text/iuls",
            "ustar" => "application/x-ustar",
            "vcf" => "text/x-vcard",
            "vrml" => "x-world/x-vrml",
            "wav" => "audio/x-wav",
            "wcm" => "application/vnd.ms-works",
            "wdb" => "application/vnd.ms-works",
            "wks" => "application/vnd.ms-works",
            "wmf" => "application/x-msmetafile",
            "wps" => "application/vnd.ms-works",
            "wri" => "application/x-mswrite",
            "wrl" => "x-world/x-vrml",
            "wrz" => "x-world/x-vrml",
            "xaf" => "x-world/x-vrml",
            "xbm" => "image/x-xbitmap",
            "xla" => "application/vnd.ms-excel",
            "xlc" => "application/vnd.ms-excel",
            "xlm" => "application/vnd.ms-excel",
            "xls" => "application/vnd.ms-excel",
            "xlt" => "application/vnd.ms-excel",
            "xlw" => "application/vnd.ms-excel",
            "xof" => "x-world/x-vrml",
            "xpm" => "image/x-xpixmap",
            "xwd" => "image/x-xwindowdump",
            "z" => "application/x-compress",
            "rar" => "application/x-rar-compressed",
            "zip" => "application/zip"
        ];
    }

    public function groups()
    {
        return response()->json(auth()->user()->getGroups());
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Exception
     */
    public function setGroup(Request $request)
    {
        $this->validate($request, [
                'id' => 'required|array',
                'group' => 'required',
            ],
            [],
            []
        );

        /** @var User $user */
        $user = auth()->user();

        foreach ($request->id as $id) {
            $file = $user->getFile($id);

            if ($file) {
                if (!$user->renameFile($file['id'], $file['name'], $request->group)) {
                    throw new \Exception(trans('mediamanager.failure_rename_media'));
                }
            }
        }

        return response()->json([
            'message' => trans('mediamanager.success_set_group_media')
        ]);
    }


    /**
     *
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function dropbox()
    {
        \SEO::setTitle(trans('mediamanager.page_title_upload_dropbox'));

        // Hat der Benutzer negatives Guthaben?
        $uploadBlocked = false;
        $funds = auth()->user()->funds;

        if ($funds < 0) {
            // Ist der aktuelle Abrechnungszeitraum nicht mehr bezahlt?

            $packageCost = Package::where('package_id', '=', auth()->user()->package_id)->value('monthly_cost');

            // Sind der Paketpreis + letzte gebuchte Betrag (Paketpreis, Zusatzoption) kleiner 0?
            // Dann war das Guthaben bereits nicht mehr ausreichend!
            if ($funds + $packageCost < 0) {
                $uploadBlocked = true;
            }
        }

        $spaceAvailable = Space::available()->owner()->sum('space_available');

        return view('media.dropbox', ['hideNav' => false, 'uploadBlocked' => $uploadBlocked, 'spaceAvailable' => $spaceAvailable]);
    }

    public function loadFromUrl(Request $request)
    {
        $validated = $this->validate($request, [
            'url' => 'required|url|active_url',
            'name' => 'required',
        ],[]);

        $user = auth()->user();
        $id = time();
        $uploadPath = get_user_media_path($user->username) . DIRECTORY_SEPARATOR . $id;

        while(File::isDirectory(storage_path($uploadPath))) {
            $id = time();
            $uploadPath = File::basename($uploadPath) . DIRECTORY_SEPARATOR . $id;
        }

        $msg = ['error' => "Die Datei `{$validated['name']}` konnte nicht übertragen werden."];

        $filename = $user->getUniqueFilename($validated['name']);
        $uploadPath .= DIRECTORY_SEPARATOR . $filename;

        if (Storage::put($uploadPath, file_get_contents($validated['url']))) {
            $file = get_file($user->username, $id);
            event(new FileSavedEvent($user, $file));
            $msg = ['success' => "Du hast die Datei `{$validated['name']}` erfolgreich in Deine Mediathek übertragen."];
        }

        return response()->json($msg);
    }
}
