<?php

namespace App\Exports;

use App\Models\Product;
use App\Enums\ProductStatus;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    /**
     * Lấy dữ liệu sản phẩm để export
     */
    public function collection()
    {
        return Product::with(['category', 'attributes.values'])
            ->latest('id')
            ->get();
    }

    /**
     * Định nghĩa headers cho file Excel
     */
    public function headings(): array
    {
        return [
            'ID',
            'Tên sản phẩm',
            'Danh mục',
            'Mô tả',
            'Giá (VND)',
            'Tồn kho',
            'Trạng thái',
            'Thuộc tính',
            'Ngày tạo',
            'Ngày cập nhật',
        ];
    }

    /**
     * Map dữ liệu từ model sang Excel
     */
    public function map($product): array
    {
        return [
            $product->id,
            $product->name,
            $product->category->name ?? 'N/A',
            $product->description ?? '',
            number_format($product->price, 0, ',', '.'),
            $product->stock,
            $product->status->label(),
            $this->formatAttributes($product),
            $product->created_at->format('d/m/Y H:i'),
            $product->updated_at->format('d/m/Y H:i'),
        ];
    }

    /**
     * Định dạng thuộc tính sản phẩm
     */
    private function formatAttributes(Product $product): string
    {
        if ($product->attributes->isEmpty()) {
            return '';
        }

        $attributes = [];
        foreach ($product->attributes as $attribute) {
            $values = $attribute->values->pluck('value')->implode(', ');
            $attributes[] = "{$attribute->name}: {$values}";
        }

        return implode('; ', $attributes);
    }

    /**
     * Thiết lập styles cho file Excel
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style cho header
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '2D5F8B']]
            ],
            // Style cho dữ liệu
            'A' => ['alignment' => ['horizontal' => 'left']],
            'D' => ['alignment' => ['wrapText' => true]],
        ];
    }

    /**
     * Thiết lập độ rộng cột
     */
    public function columnWidths(): array
    {
        return [
            'A' => 8,   // ID
            'B' => 25,  // Tên sản phẩm
            'C' => 20,  // Danh mục
            'D' => 40,  // Mô tả
            'E' => 15,  // Giá
            'F' => 10,  // Tồn kho
            'G' => 15,  // Trạng thái
            'H' => 30,  // Thuộc tính
            'I' => 18,  // Ngày tạo
            'J' => 18,  // Ngày cập nhật
        ];
    }
}