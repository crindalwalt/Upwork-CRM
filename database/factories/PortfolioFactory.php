<?php

namespace Database\Factories;

use App\Models\Portfolio;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Portfolio>
 */
class PortfolioFactory extends Factory
{
    protected $model = Portfolio::class;

    public function definition(): array
    {
        $tags = [
            'ai_agent',
            'langchain',
            'n8n',
            'fastapi',
            'chatbot',
            'automation',
            'voice_ai',
            'web_scraping',
        ];

        $techStack = [
            'Python',
            'FastAPI',
            'LangChain',
            'n8n',
            'OpenAI API',
            'Laravel',
            'Node.js',
            'Twilio',
        ];

        return [
            'title' => fake()->randomElement([
                'AI Cold Calling Platform',
                'Lead Qualification Agent',
                'Automated CRM Integration',
                'AI Customer Support Bot',
                'n8n Workflow Automation Suite',
            ]),
            'description' => fake()->paragraph(),
            'loom_url' => fake()->optional()->url(),
            'live_url' => fake()->optional()->url(),
            'github_url' => fake()->optional()->url(),
            'tags' => fake()->randomElements($tags, fake()->numberBetween(2, 5)),
            'tech_stack' => fake()->randomElements($techStack, fake()->numberBetween(3, 5)),
            'client_name' => fake()->optional()->company(),
            'client_location' => fake()->optional()->country(),
            'outcome_summary' => fake()->optional()->sentence(12),
            'is_featured' => fake()->boolean(30),
            'sort_order' => fake()->numberBetween(0, 20),
        ];
    }
}
