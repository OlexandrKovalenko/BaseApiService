<?php

namespace App\System\Facades;

use App\System\Core\ResultCodes;
use App\System\Http\RequestBundle;
use App\System\Http\ResponseBundle;
use App\System\Traits\LoggableTrait;
use App\System\Util\GuidHelper;
use App\System\Util\Log;
use App\System\Util\Validator\RequestValidator;
use App\System\Util\Validator\Validator;
use Random\RandomException;

class BaseFacade
{
    use LoggableTrait;
    protected string $globalSessionId;

    public Validator $validator;
    protected RequestValidator $requestValidator;

    /**
     * @throws RandomException
     */
    function __construct() {
        $this->validator = new Validator();
        $this->requestValidator = new RequestValidator();
        $this->globalSessionId = GuidHelper::getOrCreateGlobalSessionId();
    }

    /**
     * Перевірка обов'язкових полів у запиті.
     * Якщо поля відсутні, повертає ResponseBundle з помилкою.
     *
     * @param RequestBundle $request
     * @param array $requiredFields
     * @return bool|ResponseBundle
     */
    protected function validateRequiredFields(RequestBundle $request, array $requiredFields): bool|ResponseBundle
    {
        $isValid = $this->requestValidator->validate($request->getBody(), $requiredFields);
        if (!$isValid) {
            return new ResponseBundle(400, [
                'errors' => $this->requestValidator->getErrors(),
            ], ResultCodes::ERROR_BAD_REQUEST);
        }
        return true;
    }
    /*public function handleException(Exception $e): ResponseBundle
    {
        return new ResponseBundle(500, [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ], ResultCodes::ERROR_INTERNAL_SERVER);
    }*/
}