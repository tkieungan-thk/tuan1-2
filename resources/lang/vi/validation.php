<?php

return [
    'required' => ':attribute không được để trống.',
    'email'    => ':attribute phải là một địa chỉ email hợp lệ.',
    'unique'   => ':attribute đã được sử dụng.',
    'confirmed'=> 'Xác nhận :attribute không khớp.',
    'min'      => [
        'string' => ':attribute phải có ít nhất :min ký tự.',
    ],
    'attributes' => [
        'name'                  => 'Tên',
        'email'                 => 'Email',
        'password'              => 'Mật khẩu',
        'password_confirmation' => 'Xác nhận mật khẩu',
    ],

];