<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\PurchaseOrder;

class PurchaseOrderStatusNotification extends Notification
{
    use Queueable;

    public $purchaseOrder;
    public $status;

    public function __construct(PurchaseOrder $purchaseOrder, $status)
    {
        $this->purchaseOrder = $purchaseOrder;
        $this->status = $status;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Purchase Order Status Update: ' . $this->purchaseOrder->po_number)
            ->line('Purchase Order ' . $this->purchaseOrder->po_number . ' has been ' . $this->status)
            ->action('View Purchase Order', route('purchase-orders.show', $this->purchaseOrder->id));
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'purchase_order_status',
            'purchase_order_id' => $this->purchaseOrder->id,
            'po_number' => $this->purchaseOrder->po_number,
            'status' => $this->status,
            'vendor_name' => $this->purchaseOrder->vendor->name
        ];
    }
}
