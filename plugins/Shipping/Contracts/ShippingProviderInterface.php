<?php

namespace Plugins\Shipping\Contracts;

use App\Models\Order;

interface ShippingProviderInterface
{
    public function createShipment(Order $order): array;

    public function cancelShipment(string $barcode): bool;

    public function getPackageStatus(string $barcode): ?array;

    public function isConfigured(): bool;
}
