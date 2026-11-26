<?php

namespace App\Services;

use App\Exceptions\CustomException;
use App\Models\Inventory;
use App\Models\Product;
use Exception;

class KardexService
{
    public function getLastRecord($productId, $warehouseId)
    {
        $lastRecord = Inventory::select('*')
            ->where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->latest('id')
            ->first();

        return [
            'quantity' => $lastRecord?->quantity_balance ?? 0,
            'cost' => $lastRecord?->cost_balance ?? 0,
            'total' => $lastRecord?->total_balance ?? 0,
            'date' => $lastRecord?->created_at ?? null,
        ];
    }

    public function registerEntry($model, array $product, $warehouseId, $detail)
    {
        try {
            $lastRecord = $this->getLastRecord($product['id'], $warehouseId);

            $newQuantityBalance = $lastRecord['quantity'] + $product['quantity'];

            if ($newQuantityBalance < 0) {
                throw new CustomException("No hay suficiente stock para el producto con ID {$product['id']} en el almacén con ID {$warehouseId}.");
            }

            $newTotalBalance = $lastRecord['total'] + ($product['quantity'] * $product['price']);
            $newCostBalance = $newTotalBalance / $newQuantityBalance;

            $model->inventories()->create([
                'detail' => $detail,
                'quantity_in' => $product['quantity'],
                'cost_in' => $product['price'],
                'total_in' => $product['quantity'] * $product['price'],
                'quantity_balance' => $newQuantityBalance,
                'cost_balance' => $newCostBalance,
                'total_balance' => $newTotalBalance,
                'product_id' => $product['id'],
                'warehouse_id' => $warehouseId,
            ]);

            Product::where('id', $product['id'])->increment('stock', $product['quantity']);
        } catch (CustomException $e) {
            throw new CustomException($e->getMessage());
        }
    }

    public function registerExit($model, array $product, $warehouseId, $detail)
    {
        try {
            $lastRecord = $this->getLastRecord($product['id'], $warehouseId);

            $newQuantityBalance = $lastRecord['quantity'] - $product['quantity'];

            if ($newQuantityBalance < 0) {
                throw new CustomException("No hay suficiente stock para el producto con ID {$product['id']} en el almacén con ID {$warehouseId}.");
            }

            $newTotalBalance = $lastRecord['total'] - ($product['quantity'] * $lastRecord['cost']);
            $newCostBalance = $newTotalBalance / ($newQuantityBalance ?: 1);

            $model->inventories()->create([
                'detail' => $detail,
                'quantity_out' => $product['quantity'],
                'cost_out' => $lastRecord['cost'],
                'total_out' => $product['quantity'] * $lastRecord['cost'],
                'quantity_balance' => $newQuantityBalance,
                'cost_balance' => $newCostBalance,
                'total_balance' => $newTotalBalance,
                'product_id' => $product['id'],
                'warehouse_id' => $warehouseId,
            ]);

            Product::where('id', $product['id'])->decrement('stock', $product['quantity']);
        } catch (CustomException $e) {
            throw new CustomException($e->getMessage());
        }
    }
}
