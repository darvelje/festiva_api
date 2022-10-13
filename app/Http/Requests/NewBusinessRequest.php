<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

class NewBusinessRequest extends FormRequest
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
    public function rules(){
        return [
           'businessName' => 'required|min:2|max:255|string',
           'businessDescription' => 'required|min:5|max:255|string',
           'businessAddress' => 'required|min:2|max:255|string',
           'businessPhone' => 'required|min:8|max:16',
//         'businessEmail' => 'required|email|unique:users|max:255|string',
           'businessEmail' => 'required|email|max:255|string',
           'businessUrl' => 'required|min:5|max:255|string',
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
