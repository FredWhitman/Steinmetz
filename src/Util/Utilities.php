<?php

namespace Util;

class Utilities {

     //Method for displaying Success and Error message
    public function showMessage($type, $message)
    {
        $validTypes = ['success', 'danger', 'warning', 'info'];
        $type = in_array($type,$validTypes) ? $type : 'info';
        $safeMessage = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

        $html = '<div class="alert alert-' . $type . ' alert-dismissible fade show" role="alert">
                    <strong>' . $safeMessage . '</strong> 
                    <button type="button" class="btn-close" data-bs-dismiss="alert" 
                    aria-label="Close"></button>
                </div>';
        return json_encode([
            'type' => $type,
            'message' => $safeMessage,
            'html' => $html
        ]);
    }
}
