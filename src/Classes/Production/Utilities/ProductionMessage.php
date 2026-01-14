<?php
// File: src/Classes/Production/Utilities/ProductionMessage.php

namespace Production\Utilities;

use Util\MessageBase;

class ProductionMessage extends MessageBase
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
