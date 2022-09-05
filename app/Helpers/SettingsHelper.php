<?php

namespace App\Helpers;

class SettingsHelper
{
    /**
     * @param string $configPath
     *
     * @return array
     */
    public static function getEmailsArrayFromConfig($configPath): array
    {
        $emailConfig = config($configPath);

        if (empty($emailConfig) || is_string($emailConfig) === false) {
            return [];
        }

        $validEmails = [];
        $emailArray = array_filter(array_map('trim', explode(',', $emailConfig)));

        foreach ($emailArray as $email) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $validEmails[] = $email;
            }
        }

        return $validEmails;
    }
}
