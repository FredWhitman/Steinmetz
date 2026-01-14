<?php
//File: src/Util/MessageBase.php

namespace Util;

abstract class MessageBase
{
    protected function buildHtml(string $type, string $message): string
    {
        $safe = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
        return " 
            <div class='alert alert-{$type} alert-dismissible fade show' role='alert'> 
                <strong>{$safe}</strong> 
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button> 
            </div> ";
    }
}
