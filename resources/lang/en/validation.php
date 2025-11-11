<?php

return [

    'required'  => 'The :attribute field is required.',
    'email'     => 'The :attribute must be a valid email address.',
    'unique'    => 'The :attribute has already been taken.',
    'confirmed' => 'The :attribute confirmation does not match.',
    'min'       => [
        'string'  => 'The :attribute field must have at least :min characters.',
        'numeric' => 'The :attribute field must be greater than or equal to :min.',
    ],
    'max' => [
        'string' => 'The :attribute field must not exceed :max characters.',
        'file'   => 'The :attribute file must not be larger than :max KB.',
    ],
    'numeric' => 'The :attribute field must be a number.',
    'integer' => 'The :attribute field must be an integer.',
    'image'   => 'The :attribute field must be an image.',
    'mimes'   => 'The :attribute field must have the format: :values.',

    'attributes' => [
        'name'                  => 'Name',
        'email'                 => 'Email address',
        'password'              => 'Password',
        'password_confirmation' => 'Password confirmation',

        'category_id'         => 'Category',
        'price'               => 'Price',
        'stock'               => 'Stock',
        'status'              => 'Status',
        'description'         => 'Product Description',
        'images'              => 'Images',
        'images.*'            => 'Image File',
        'attributes.*.name'   => 'Attribute Name',
        'attributes.*.values' => 'Attribute Values',    ],

];
