<?php

namespace Movie\Api\Auth\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Movie\Api\Auth\Services\LoginService;

class LoginController extends Controller
{

    /**
     * @var loginService
     */
    protected $loginService;

    /**
     * Login constructor
     *
     * @param LoginService
     */
    public function __construct(LoginService $loginService)
    {
        $this->loginService = $loginService;
    }

    /**
     * User login API method
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->only('email', 'password'), [
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return sendError('Validation Error.', $validator->errors(), 400);
        }
        $credentials = $request->only('email', 'password');

        $result = $this->loginService->createToken($credentials);
        return $result;
    }
}
