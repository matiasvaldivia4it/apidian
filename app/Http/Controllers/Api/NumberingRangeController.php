<?php

namespace App\Http\Controllers\Api;

use App\User;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\NumberingRangeRequest;
use ubl21dian\Templates\SOAP\GetNumberingRange;
use App\Traits\DocumentTrait;

class NumberingRangeController extends Controller
{
    /**
     * NumberingRange.
     *
     * @param \App\Http\Requests\Api\NumberingRangeRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function NumberingRange(NumberingRangeRequest $request)
    {
        // User
        $user = auth()->user();
        $company = $user->company;

        // Verify Certificate
        $certificate_days_left = 0;
        $c = $this->verify_certificate();
        if(!$c['success'])
            return $c;
        else
            $certificate_days_left = $c['certificate_days_left'];

        $getNumberingRange = new GetNumberingRange($user->company->certificate->path, $user->company->certificate->password);
        $getNumberingRange->Nit = $company->identification_number;
        $getNumberingRange->IDSoftware = $request->IDSoftware;

        if ($request->GuardarEn)
          return [
              'message' => 'Consulta generada con éxito',
              'ResponseDian' => $getNumberingRange->signToSend($request->GuardarEn.'\\Req-NumbRg.xml')->getResponseToObject($request->GuardarEn.'\\Rpta-NumbRg.xml'),
              'certificate_days_left' => $certificate_days_left,
            ];
        else
          return [
              'message' => 'Consulta generada con éxito',
              'ResponseDian' => $getNumberingRange->signToSend()->getResponseToObject(),
              'certificate_days_left' => $certificate_days_left,
            ];
    }
}
