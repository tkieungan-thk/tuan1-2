<?php

return [
    'required'  => ':attribute không được để trống.',
    'email'     => ':attribute phải là một địa chỉ email hợp lệ.',
    'unique'    => ':attribute đã được sử dụng.',
    'confirmed' => 'Xác nhận :attribute không khớp.',
    'min'       => [
        'string'  => 'Trường :attribute phải có ít nhất :min ký tự.',
        'numeric' => 'Trường :attribute phải lớn hơn hoặc bằng :min.',
    ],
    'max' => [
        'string' => 'Trường :attribute không được vượt quá :max ký tự.',
        'file'   => 'Tệp :attribute không được lớn hơn :max KB.',
    ],
    'numeric' => 'Trường :attribute phải là số.',
    'integer' => 'Trường :attribute phải là số nguyên.',
    'image'   => 'Trường :attribute phải là hình ảnh.',
    'mimes'   => 'Trường :attribute phải có định dạng: :values.',

    'attributes' => [
        'name'                  => 'Tên',
        'email'                 => 'Email',
        'password'              => 'Mật khẩu',
        'password_confirmation' => 'Xác nhận mật khẩu',

        'category_id'         => 'Danh mục',
        'price'               => 'Giá',
        'stock'               => 'Số lượng tồn kho',
        'status'              => 'Trạng thái',
        'description'         => 'Mô tả sản phẩm',
        'images'              => 'Hình ảnh',
        'images.*'            => 'Tệp hình ảnh',
        'attributes.*.name'   => 'Tên thuộc tính',
        'attributes.*.values' => 'Giá trị thuộc tính',

    ],

];
