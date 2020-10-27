<?php

namespace Uteq\Move\Concerns;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;

trait HasRules
{
    /**
     * The validation rules for creation and updates.
     */
    public array $rules = [];

    /**
     * The validation rules for creation.
     */
    public array $creationRules = [];

    /**
     * The validation rules for updates.
     */
    public array $updateRules = [];

    /**
     * Validation rules with a custom attribute
     */
    public array $customRules = [];

    /**
     * Set the validation rules for the field.
     *
     * @param  callable|array|string  $rules
     */
    public function rules($rules): self
    {
        $this->rules = ($rules instanceof Rule || is_string($rules)) ? func_get_args() : $rules;

        return $this;
    }

    public function addCustomRule($attribute, $rule)
    {
        $this->customRules[$attribute] = is_callable($rule)
            ? call_user_func($rule, request())
            : $rule;
    }

    public function customRules($rules)
    {
        return $this->customRules = array_replace_recursive(
            $this->customRules,
            $rules
        );
    }

    /**
     * Get the validation rules for this field.
     * @return array
     */
    public function getRules(Request $request)
    {
        return array_replace_recursive($this->customRules, [
            $this->attribute => is_callable($this->rules)
                ? call_user_func($this->rules, $request)
                : $this->rules,
        ]);
    }

    /**
     * Get the creation rules for this field.
     * @return array|string
     */
    public function getCreationRules(Request $request)
    {
        $rules = [
            $this->attribute => is_callable($this->creationRules)
                ? call_user_func($this->creationRules, $request)
                : $this->creationRules,
        ];

        return array_merge_recursive($this->getRules($request), $rules);
    }

    /**
     * Set the creation validation rules for the field.
     *
     * @param  callable|array|string  $rules
     */
    public function creationRules($rules): self
    {
        $this->creationRules = ($rules instanceof Rule || is_string($rules)) ? func_get_args() : $rules;

        return $this;
    }

    /**
     * Get the update rules for this field.
     * @return array
     */
    public function getUpdateRules(Request $request)
    {
        $rules = [$this->attribute => is_callable($this->updateRules)
            ? call_user_func($this->updateRules, $request)
            : $this->updateRules, ];

        return array_merge_recursive(
            $this->getRules($request),
            $rules
        );
    }

    /**
     * Set the creation validation rules for the field.
     *
     * @param  callable|array|string  $rules
     * @return $this
     */
    public function updateRules($rules)
    {
        $this->updateRules = ($rules instanceof Rule || is_string($rules)) ? func_get_args() : $rules;

        return $this;
    }
}
