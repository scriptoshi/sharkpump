<?php

namespace App\Enums;

enum BotProvider: string
{
    case ANTHROPIC = 'anthropic';
    case OPENAI = 'openai';
    case GEMINI = 'gemini';
    //case DEEPSEEK = 'deepseek';

    /**
     * Get a description for the bot provider.
     * 
     * @return string
     */
    public function description(): string
    {
        return match ($this) {
            self::ANTHROPIC => 'Anthropic Claude',
            self::OPENAI => 'OpenAI ChatGPT',
            self::GEMINI => 'Google Gemini',
            //self::DEEPSEEK => 'DeepSeek',
        };
    }
}
