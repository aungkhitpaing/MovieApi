<?php

namespace Movie\Api\Auth\Services;

use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;
use Illuminate\Http\JsonResponse;

class LoginService
{
    /**
     * Create Token For Login User
     *
     * @param $params
     * @return JsonResponse
     */
    public function createToken(array $params): JsonResponse
    {

        if (Auth::attempt($params)) {
            $user = Auth::user();

            $response = sendResponse($this->loginResponse($user), 200);

        } else {
            $message = new MessageBag();
            $message->add('Invalid params', 'User can not access permission !');
            $response = sendError('Unauthorised', $message, 401);
        }

        return $response;
    }

    /**
     * Return Login Success Response
     *
     * @param User $user
     * @return array
     */
    private function loginResponse(User $user): array
    {
        return [
            'name' => $user->name,
            'email' => $user->email,
            'token' => $user->createToken('accessToken')->accessToken
        ];
    }
}
