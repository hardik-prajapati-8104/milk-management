<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            ['company', 'company_name', 'My Milk Dairy', 'text'],
            ['company', 'company_logo', null, 'image'],
            ['company', 'gst_number', null, 'text'],
            ['company', 'company_address', null, 'textarea'],
            ['company', 'company_phone', null, 'text'],
            ['company', 'company_email', null, 'text'],
            ['company', 'company_website', null, 'text'],
            ['invoice', 'invoice_prefix', 'INV', 'text'],
            ['invoice', 'consumer_prefix', 'MILK', 'text'],
            ['invoice', 'receipt_prefix', 'RCPT', 'text'],
            ['general', 'currency_symbol', '₹', 'text'],
            ['general', 'timezone', 'Asia/Kolkata', 'text'],
            ['general', 'language', 'en', 'text'],
            ['sms', 'sms_provider', 'none', 'text'], // twilio | msg91 | fast2sms | none
            ['whatsapp', 'whatsapp_enabled', '0', 'boolean'],
            ['backup', 'backup_reminder_days', '7', 'text'],
        ];

        foreach ($defaults as [$group, $key, $value, $type]) {
            Setting::firstOrCreate(['key' => $key], compact('group', 'value', 'type'));
        }
    }
}
