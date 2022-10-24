<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class NewUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'userName' => 'required|min:3|max:255|string',
            'userLastName' => 'required|min:3|max:255|string',
            'userPhone' => 'required|min:8|max:10|',
            'userEmail' => 'required|max:255|string|email|unique:users,email',
            'userPassword' => 'required|max:255|string',
        ];
    }

    public function response(array $errors){
        if($this->expectsJson()){
            return new JsonResponse($errors, 422);
        }
        return $this->redirector->to($this->getRedirectUrl())
            ->withInput($this->except($this->dontFlash))
            ->withErrors($errors, $this->errorBag);
    }

    protected function failedValidation(Validator $validator){
        $errors = (new ValidationException($validator))->errors();

        throw new HttpResponseException(
            response()->json(['errors' => $errors], Jsonresponse::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
