<?php

namespace App\Enums;

enum MessageRole: string
{
    case USER = 'user';
    case ASSISTANT = 'assistant';
    case SYSTEM = 'system';
    case MODEL = 'model';
}
