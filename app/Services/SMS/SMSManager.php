<?php

namespace App\Services\SMS;

use App\Services\SmsService;

class SMSManager
{
    public function __construct(private readonly SmsService $smsService)
    {
    }

    public function send(string $phoneNumber, string $message): array
    {
        return $this->smsService->sendMessage($phoneNumber, $message);
    }

    public function sendTemplate(string $phoneNumber, string $templateName, array $parameters = []): array
    {
        $template = config("sms.templates.{$templateName}");

        if (!is_string($template) || trim($template) === '') {
            return [
                'success' => false,
                'error' => "SMS template [{$templateName}] not found.",
                'provider' => null,
                'recipient' => $phoneNumber,
            ];
        }

        $message = $this->renderTemplate($template, $parameters);

        return $this->send($phoneNumber, $message);
    }

    private function renderTemplate(string $template, array $parameters): string
    {
        $replacements = [];

        foreach ($parameters as $key => $value) {
            $replacements['{' . $key . '}'] = (string) $value;
        }

        return strtr($template, $replacements);
    }
}