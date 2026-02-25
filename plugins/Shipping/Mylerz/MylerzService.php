<?php

namespace Plugins\Shipping\Mylerz;

use App\Models\Order;
use App\Models\Governorate;
use Plugins\Shipping\Contracts\ShippingProviderInterface;
use Illuminate\Support\Facades\Log;

class MylerzService implements ShippingProviderInterface
{
    protected MylerzClient $client;

    public function __construct(?MylerzClient $client = null)
    {
        $this->client = $client ?? new MylerzClient();
    }

    public function isConfigured(): bool
    {
        return !empty(config('plugins.mylerz.username'))
            && !empty(config('plugins.mylerz.password'))
            && !empty(config('plugins.mylerz.warehouse'));
    }

    public function createShipment(Order $order): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'error' => 'Mylerz is not configured'];
        }

        if (empty($order->tracking_id)) {
            return ['success' => false, 'error' => 'Order has no tracking ID (not from website)'];
        }

        $mylerzOrder = $this->mapOrderToMylerz($order);
        $result = $this->client->addOrders([$mylerzOrder]);

        if (!$result['success']) {
            Log::error('Mylerz create shipment failed', [
                'order_id' => $order->id,
                'tracking_id' => $order->tracking_id,
                'error' => $result['error'] ?? 'Unknown',
            ]);
            return $result;
        }

        $barcode = $result['packages'][0]['BarCode'] ?? null;
        if (!$barcode) {
            return ['success' => false, 'error' => 'No barcode in response'];
        }

        $pickupResult = $this->client->createPickup([$barcode]);

        $shippingData = [
            'provider' => 'mylerz',
            'barcode' => $barcode,
            'pickup_created' => $pickupResult['success'] ?? false,
        ];
        if (!empty($pickupResult['pickups'][0]['PickupOrderCode'])) {
            $shippingData['pickup_order_code'] = $pickupResult['pickups'][0]['PickupOrderCode'];
        }

        return [
            'success' => true,
            'barcode' => $barcode,
            'shipping_data' => $shippingData,
        ];
    }

    public function cancelShipment(string $barcode): bool
    {
        $result = $this->client->cancelPackage($barcode);
        return $result['success'] ?? false;
    }

    public function getPackageStatus(string $barcode): ?array
    {
        return $this->client->getPackageStatus($barcode);
    }

    protected function mapOrderToMylerz(Order $order): array
    {
        // Same format as create-mylerz-order.php: +201234567890
        $phone = preg_replace('/[^0-9]/', '', $order->customer_numbers[0] ?? '');
        $phone = ltrim($phone, '0');
        if (!str_starts_with($phone, '20')) {
            $phone = '20' . (strlen($phone) === 10 ? $phone : substr($phone, -10));
        }
        $phone = '+' . $phone;

        // Neighborhood = zone code (GetCityZoneList). Default NASR as in create-mylerz-order.php
        $zoneCode = config('plugins.mylerz.default_zone', 'NASR');
        if ($order->governorate_id) {
            $gov = Governorate::find($order->governorate_id);
            if ($gov && !empty($gov->mylerz_zone_code)) {
                $zoneCode = $gov->mylerz_zone_code;
            }
        }

        $description = collect($order->items ?? [])->map(function ($item) {
            return ($item['product_name'] ?? '') . ' x' . ($item['quantity'] ?? 1);
        })->implode(', ');

        // Payload structure must match create-mylerz-order.php exactly (no City field)
        return [
            'WarehouseName' => config('plugins.mylerz.warehouse'),
            'PickupDueDate' => now()->format('Y-m-d H:i:s'),
            'Package_Serial' => 1,
            'Reference' => (string) $order->tracking_id,
            'Description' => $description ?: 'Order ' . $order->tracking_id,
            'Service_Type' => config('plugins.mylerz.service_type', 'DTD'),
            'Service' => 'ND',
            'Service_Category' => 'DELIVERY',
            'Payment_Type' => $order->payment_method === 'cod' ? 'COD' : 'PP',
            'COD_Value' => $order->payment_method === 'cod'
                ? (float) $order->total_amount
                : (float) max(0, $order->total_amount - ($order->delivery_fees ?? 0)),
            'Pieces' => [
                ['PieceNo' => 1, 'Special_Notes' => ''],
            ],
            'Customer_Name' => trim($order->customer_name),
            'Mobile_No' => $phone,
            'Street' => trim($order->customer_address) ?: 'عنوان التوصيل',
            'Country' => config('plugins.mylerz.country') === 'EG' ? 'Egypt' : 'Tunisia',
            'Neighborhood' => $zoneCode,
            'Address_Category' => config('plugins.mylerz.address_category', 'H'),
            'Currency' => 'EGP',
        ];
    }
}
