<?php

namespace App\Http\Requests;

use Auth;
use Gate;
use Illuminate\Contracts\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;

abstract class Request extends FormRequest
{
    const CREATE = 'create';
    const UPDATE = 'update';
    const DELETE = 'delete';
    const OTHER = 'other';

    /**
     * @var string Model binder key
     */
    protected $modelKey;

    /**
     * @return bool
     */
    protected function checkPolicies()
    {
        if ($this->methodType() == self::CREATE) {
            return Gate::allows($this->methodType(), $this->modelFromRequest(true));
        }

        return Gate::allows($this->methodType(), $this->modelFromRequest());
    }

    /**
     * @param array $rules
     * @return array
     */
    protected function processPlaceholders($rules)
    {
        if (!$this->modelFromRequest()) {
            return $rules;
        }

        $modelId = $this->modelFromRequest()->id;

        return array_map(function($item) use ($modelId) {
            return str_replace(':id', $modelId, $item);
        }, $rules);
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (!Auth::check()) {
            return false;
        }

        if (empty($this->modelKey)) {
            abort(403, 'No model key specified for request');
        }

        return $this->checkPolicies();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = array_get($this->rules, $this->methodType(), []);

        if (empty($rules)) {
            abort(403, sprintf('No rules specified for %s method', strtoupper($this->methodType())));
        }

        return $this->processPlaceholders($rules);
    }

    /**
     * @param bool $orNew
     * @return Model|null
     */
    protected function modelFromRequest($orNew = false)
    {
        $model = $this->route($this->modelKey);

        if ($model || !$orNew) {
            return $model;
        }

        $modelName = ucfirst(str_singular($this->modelKey));
        $fullModelName = "\\App\\Models\\{$modelName}";

        return new $fullModelName();
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param Validator $validator
     * @return mixed
     */
    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator);
    }

    /**
     * Handle a failed authorization attempt.
     *
     * @return mixed
     */
    protected function failedAuthorization()
    {
        abort(403, 'Forbidden');
    }

    /**
     * @return string
     */
    protected function methodType()
    {
        switch ($this->method()) {
            case 'POST':
                return self::CREATE;
            case 'PUT':
            case 'PATCH':
                return self::UPDATE;
            case 'DELETE':
                return self::DELETE;
            default:
                return self::OTHER;
        }
    }
}
