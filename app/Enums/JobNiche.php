<?php

namespace App\Enums;

enum JobNiche: string
{
    case AiAgent = 'ai_agent';
    case AiAutomation = 'ai_automation';
    case FullStackWeb = 'full_stack_web';
    case MobileApp = 'mobile_app';
    case Chatbot = 'chatbot';
    case WebScraping = 'web_scraping';
    case ApiIntegration = 'api_integration';
    case SaasProduct = 'saas_product';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::AiAgent => 'AI Agent Development',
            self::AiAutomation => 'AI Automation',
            self::FullStackWeb => 'Full Stack Web Development',
            self::MobileApp => 'Mobile App Development',
            self::Chatbot => 'Chatbot Development',
            self::WebScraping => 'Web Scraping',
            self::ApiIntegration => 'API Integration',
            self::SaasProduct => 'SaaS Product Development',
            self::Other => 'Other',
        };
    }
}
