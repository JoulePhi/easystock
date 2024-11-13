<?php

namespace App\Notifications;

use App\Models\Item;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class LowStockAlert extends Notification
{
    use Queueable;

    protected $item;

    public function __construct(Item $item)
    {
        $this->item = $item;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Low Stock Alert - ' . $this->item->name)
            ->line('The stock level for ' . $this->item->name . ' is below the minimum threshold.')
            ->line('Current stock: ' . $this->item->current_stock . ' ' . $this->item->unit)
            ->line('Minimum stock: ' . $this->item->minimum_stock . ' ' . $this->item->unit)
            ->action('View Item', route('inventory.items.edit', $this->item->id));
    }

    public function toArray($notifiable)
    {
        return [
            'item_id' => $this->item->id,
            'item_name' => $this->item->name,
            'current_stock' => $this->item->current_stock,
            'minimum_stock' => $this->item->minimum_stock,
            'unit' => $this->item->unit
        ];
    }
}
