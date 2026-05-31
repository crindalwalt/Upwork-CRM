<?php

namespace Database\Factories;

use App\Enums\BudgetType;
use App\Enums\JobDifficulty;
use App\Enums\JobNiche;
use App\Models\Employer;
use App\Models\Job;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Job>
 */
class JobFactory extends Factory
{
    protected $model = Job::class;

    public function definition(): array
    {
        $budgetType = fake()->randomElement(BudgetType::cases());
        $fixedMin = fake()->randomFloat(2, 200, 1000);
        $fixedMax = fake()->randomFloat(2, max($fixedMin + 100, 1000), 5000);
        $hourlyMin = fake()->randomFloat(2, 20, 60);
        $hourlyMax = fake()->randomFloat(2, max($hourlyMin + 5, 35), 120);

        return [
            'employer_id' => Employer::factory(),
            'upwork_job_id' => fake()->optional()->bothify('JOB-########'),
            'title' => fake()->randomElement([
                'Build AI Agent for Lead Qualification',
                'n8n Automation Specialist Needed',
                'LangChain Developer for Custom Chatbot',
                'AI Voice Assistant for Outbound Sales',
                'Full Stack CRM Automation Engineer',
            ]),
            'url' => 'https://www.upwork.com/jobs/~'.fake()->bothify('??????????'),
            'description' => fake()->paragraphs(3, true),
            'niche' => fake()->randomElement(JobNiche::cases())->value,
            'budget_type' => $budgetType->value,
            'budget_min' => $budgetType === BudgetType::Fixed ? $fixedMin : null,
            'budget_max' => $budgetType === BudgetType::Fixed ? $fixedMax : null,
            'hourly_rate_min' => $budgetType === BudgetType::Hourly ? $hourlyMin : null,
            'hourly_rate_max' => $budgetType === BudgetType::Hourly ? $hourlyMax : null,
            'posted_at' => fake()->dateTimeBetween('-7 days', 'now'),
            'proposals_count_at_time' => fake()->numberBetween(0, 50),
            'difficulty' => fake()->optional()->randomElement(JobDifficulty::cases())?->value,
            'required_skills' => fake()->randomElements([
                'OpenAI API',
                'LangChain',
                'Laravel',
                'Python',
                'FastAPI',
                'Twilio',
                'n8n',
                'Web Scraping',
            ], fake()->numberBetween(2, 5)),
            'is_featured' => fake()->boolean(20),
        ];
    }

    public function recent(): static
    {
        return $this->state(fn (array $attributes) => [
            'posted_at' => fake()->dateTimeBetween('-24 hours', 'now'),
        ]);
    }

    public function highBudget(): static
    {
        return $this->state(fn (array $attributes) => [
            'budget_type' => BudgetType::Fixed->value,
            'budget_min' => fake()->randomFloat(2, 1000, 2500),
            'budget_max' => fake()->randomFloat(2, 3000, 8000),
            'hourly_rate_min' => null,
            'hourly_rate_max' => null,
        ]);
    }
}
