<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

// This is how I will ultimately solve the issue of more complicated validation.

// The goal of BoxDayCombo is to check that an entry in the database doesn't already exist with the combined value of two fields.
// We can expect to have the same box name multiple times in the fruitbox tables for example (daily fruit deliveries) but that name should only appear at max once per delivery day.
// I.e That a box name and delivery day combo doesn't already exist.

class BoxDayCombo implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // This is where the logic was going to go but I found a short term solution of making a db check with the box name and delivery day combo.
        // If we get a match, send a slack notification warning that a duplicate entry already exists, otherwise create the new entry.
        // This isn't real validation however and other than checking the slack feed for laravel logs, the user doesn't know whether the fruitbox creation was actually successful or not!
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The validation error message.';
    }
}
