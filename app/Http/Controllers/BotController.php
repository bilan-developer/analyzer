<?php

namespace App\Http\Controllers;

use App\Http\Requests\SiteRequest;
use App\Site;
use Illuminate\Http\Request;

class BotController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('bot/index');
    }


    public function analysis(SiteRequest $request, Site $model)
    {
        try {
            $model->fill($request->only('url'));
            $model->parsingUrl();
            $checkHeaders = $model->checkHeaders();
            if(!$checkHeaders['status']){
                $fileName = $model->saveReport($checkHeaders['report']);
                return view('bot/report', ['report' => $checkHeaders['report'], 'fileName' => $fileName]);
            }

            $report = $model->searchFile();
            $fileName = $model->saveReport($report);
            return view('bot/report', ['report' => $report, 'fileName' => $fileName]);
        } catch(\Exception $e) {
            return $this->getErrorResponse($e);
        }
    }

    public function save(Request $request, Site $model)
    {
        try {
            $model->createdExcel($request->name);
        } catch(\Exception $e) {
            return $this->getErrorResponse($e);
        }
    }


}
