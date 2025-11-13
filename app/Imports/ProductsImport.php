<?php

namespace App\Imports;

use App\Enums\ProductType;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProductsImport implements ToCollection, WithHeadingRow, WithValidation
{
    /**
     * Số dòng import thành công
     */
    private $successCount = 0;

    /**
     * Lỗi trong quá trình import
     */
    private $errors = [];

    /**
     * Xử lý dữ liệu từ Excel
     */
    public function collection(Collection $rows)
    {
        DB::beginTransaction();

        try {
            foreach ($rows as $index => $row) {
                if ($this->isEmptyRow($row)) {
                    continue;
                }

                $rowNumber = $index + 2;

                try {
                    $validator = Validator::make($row->toArray(), $this->rules());

                    if ($validator->fails()) {
                        $this->errors[] = "Dòng {$rowNumber}: " . implode(', ', $validator->errors()->all());

                        continue;
                    }

                    $category = Category::firstOrCreate(
                        ['name' => trim($row['danh_muc'])],
                        ['name' => trim($row['danh_muc'])]
                    );

                    $product = Product::create([
                        'name' => $row['ten_san_pham'],
                        'category_id' => $category->id,
                        'description' => $row['mo_ta'] ?? '',
                        'price' => $this->parsePrice($row['gia_vnd']),
                        'stock' => intval($row['ton_kho']),
                        'status' => $this->parseStatus($row['trang_thai']),
                    ]);

                    if (!empty($row['thuoc_tinh'])) {
                        $this->processAttributes($product, $row['thuoc_tinh']);
                    }

                    $this->successCount++;
                } catch (\Exception $e) {
                    $this->errors[] = "Dòng {$rowNumber}: " . $e->getMessage();
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->errors[] = 'Lỗi hệ thống: ' . $e->getMessage();
        }
    }

    /**
     * Kiểm tra dòng có trống không
     */
    private function isEmptyRow($row): bool
    {
        return empty(array_filter($row->toArray(), function ($value) {
            return $value !== null && $value !== '';
        }));
    }

    /**
     * Parse giá từ string sang number
     */
    private function parsePrice(string $price): float
    {
        $cleaned = preg_replace('/[^\d]/', '', $price);

        return floatval($cleaned);
    }

    /**
     * Parse trạng thái từ string sang enum
     */
    private function parseStatus(string $status): string
    {
        $statusMap = [
            'hoạt động' => ProductType::ACTIVE->value,
            'active' => ProductType::ACTIVE->value,
            'không hoạt động' => ProductType::INACTIVE->value,
            'inactive' => ProductType::INACTIVE->value,
            'nháp' => ProductType::DRAFT->value,
            'draft' => ProductType::DRAFT->value,
        ];

        $lowerStatus = strtolower(trim($status));

        return $statusMap[$lowerStatus] ?? ProductType::DRAFT->value;
    }

    /**
     * Xử lý thuộc tính sản phẩm
     */
    private function processAttributes(Product $product, string $attributesString): void
    {
        $attributes = array_filter(explode(';', $attributesString));

        foreach ($attributes as $attribute) {
            $parts = explode(':', $attribute, 2);

            if (count($parts) === 2) {
                $name = trim($parts[0]);
                $values = array_map('trim', explode(',', $parts[1]));

                if (!empty($name) && !empty($values)) {
                    $attributeModel = Attribute::create([
                        'name' => $name,
                        'product_id' => $product->id,
                    ]);

                    foreach ($values as $value) {
                        if (!empty($value)) {
                            AttributeValue::create([
                                'attribute_id' => $attributeModel->id,
                                'value' => $value,
                            ]);
                        }
                    }
                }
            }
        }
    }

    /**
     * Rules validation cho import
     */
    public function rules(): array
    {
        return [
            'ten_san_pham' => 'required|string|max:255',
            'danh_muc' => 'required|string|max:255',
            'gia_vnd' => 'required',
            'ton_kho' => 'required|integer|min:0',
            'trang_thai' => 'required|string',
        ];
    }

    /**
     * Custom validation messages
     */
    public function customValidationMessages(): array
    {
        return [
            'ten_san_pham.required' => 'Tên sản phẩm là bắt buộc',
            'danh_muc.required' => 'Danh mục là bắt buộc',
            'gia_vnd.required' => 'Giá là bắt buộc',
            'ton_kho.required' => 'Tồn kho là bắt buộc',
            'trang_thai.required' => 'Trạng thái là bắt buộc',
        ];
    }

    /**
     * Lấy số lượng import thành công
     */
    public function getSuccessCount(): int
    {
        return $this->successCount;
    }

    /**
     * Lấy danh sách lỗi
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}