<?php
// File: src/Classes/Inventory/Utilities/InventoryMessage.php

namespace Inventory\Utilities;

use Util\MessageBase;

class InventoryMessage extends MessageBase
{
    public function showMessage(string $type, string $message): array
    {
        $validTypes = ['success', 'info', 'warning', 'danger'];
        $type = in_array($type, $validTypes) ? $type : 'info';

        return [
            'type'    => $type,
            'message' => $message,
            'html'    => $this->buildHtml($type, $message)
        ];
    }
}
