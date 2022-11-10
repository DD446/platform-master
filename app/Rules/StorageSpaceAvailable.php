<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class StorageSpaceAvailable implements Rule
{
    private $missing;

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
        $availableSpace = auth()->user()->getAvailableStorage();

        if ($value instanceof \Illuminate\Http\UploadedFile) {
            $filesize = $value->getSize();
        } else {
            $filesize = $value;
        }

        if ($availableSpace < $filesize) {
            $this->missing = get_size_readable($filesize - $availableSpace);
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.storagespace', ['missing' => $this->missing]);
    }
}
