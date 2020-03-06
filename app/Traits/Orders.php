<?php

namespace App\Traits;

use App\OrderItem;
use App\Product;

trait Orders
{
    //
    public function addProductToBox($request)
    {
        $addProduct = new OrderItem();
        $addProduct->product_id = request('product.id'); // This can be passed from the frontend, depending on which product has been selected.
        $addProduct->quantity = request('product.quantity'); // This can be passed from the frontend, depending on which product has been selected.
        $addProduct->box_id = request('box_details.id'); // This can be passed from the frontend, depending on which box is being edited.
        $addProduct->box_type = "App\\" . request('box_type'); // This can be passed from the frontend, depending on which form is being submitted.
        $addProduct->delivery_date = request('box_details.delivery_week'); // This can be passed from the frontend, depending on which box is being edited.
        $addProduct->save();

        // Now we just need to adjust the stock levels.
        Product::find(request('product.id'))->decrement('stock_level', request('product.quantity'));
    }

    public function removeProductFromBox($id)
    {
        // Get what we need to adjust the stock levels
        $item = OrderItem::find($id);

        // Destroy the item from the box.
        OrderItem::destroy($id);

        // Now we just need to adjust the stock levels.
        Product::find($item->product_id)->increment('stock_level', $item->quantity);
    }

    public function increaseOrderItemQuantity($request)
    {
        // Grab the existing item.
        $item = OrderItem::find(request('item_id'));

        // Update the quanity field for this item.
        $item->update([
            'quantity' => request('item_quantity'),
        ]);

        // Now we need to adjust the product quantity to keep things accurate.
        if ($item->quantity > request('item_quantity')) {
            // New quantity is higher, we need to remove more from stock
            $adjustment = ($item->quantity - request('item_quantity'));
            Product::find($item->product_id)->decrement('stock_level', $adjustment);
        } elseif ($item->quantity < request('item_quantity')) {
            // New quantity is less than before, so we can return some stock.
            $adjustment = (request('item_quantity') - $item->quantity);
            Product::find($item->product_id)->increment('stock_level', $adjustment);
        } else {
            // Then the number must be the same, presumably the user is confused.
        }
    }
}
