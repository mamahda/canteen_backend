<?php

namespace App\Http\Controllers;

use App\Http\Controllers;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderAdminController
{
  /**
   * FITUR GET
   * Mendapatkan list order smua user
   */
  public function showAllOrder(){
    $order = Order::with(['user', 'menus'])->latest()->get();

    return response()->json([
      'success' => true,
      'message' => 'Show all user order',
      'data' => $order
    ], 200);
  }

  /**
   * Mengupdate status pesanan.
   * Endpoint ini menangani semua perubahan status oleh admin,
   * termasuk menerima/menolak permintaan pembatalan.
   */
  public function updateStatus(Request $request, Order $order)
  {
    /**
     * Validasi: Pastikan status yang dikirim adalah salah satu
     * dari nilai yang diizinkan.
     */
    $validatedData = $request->validate([
      'status' => ['required', 'string', Rule::in([
        'Pending',
        'Processing',
        'Completed',
        'Cancelled'
      ])],
    ]);

    $newStatus = $validatedData['status'];
    $currentStatus = $order->status;

    /** Logika untuk Menangani Permintaan Pembatalan (Request Cancellation) */
    if ($currentStatus === 'Requect Cancellation') {
      if (!in_array($newStatus, ['Cancelled', 'Pending'])) {
        return response()->json([
          'success' => false,
          'message' => "Invalid action. To handle a cancellation request, please set status to 'Cancelled' (to accept) or 'Pending' (to decline)."
        ], 422); 
      }
    }

    /** Update status pesanan di database. */
    $order->update(['status' => $newStatus]);

    return response()->json([
      'success' => true,
      'message' => 'Order status updated successfully.',
      'data' => $order
    ], 200);
  }
}
