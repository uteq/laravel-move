<?php

namespace Uteq\Move\Controllers;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DownloadController extends Controller
{
    use ValidatesRequests;

    /**
     * @param Request         $request
     * @param ResponseFactory $response
     *
     * @return BinaryFileResponse
     * @throws ValidationException
     */
    public function __invoke(Request $request, ResponseFactory $response): BinaryFileResponse
    {
        $data = $this->validate($request, [
            'path' => 'required',
            'filename' => 'required',
        ]);

        return $response->download(
            decrypt($data['path']),
            $data['filename']
        )->deleteFileAfterSend(true);
    }
}
