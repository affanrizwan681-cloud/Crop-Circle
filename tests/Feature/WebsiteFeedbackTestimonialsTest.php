<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\WebsiteFeedback;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebsiteFeedbackTestimonialsTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_scope_returns_only_consented_reviewed_featured_feedback_for_matching_active_users(): void
    {
        $customer = User::factory()->create(['role' => 'customer', 'status' => 'active']);
        $farmer = User::factory()->create(['role' => 'farmer', 'status' => 'active']);

        $featuredCustomerReview = $this->feedback($customer, 'customer', 'review', 'reviewed', true, true);
        $this->feedback($farmer, 'farmer', 'review', 'reviewed', false, true);
        $this->feedback($farmer, 'farmer', 'review', 'new', true, true);
        $this->feedback($customer, 'customer', 'issue', 'reviewed', true, true);
        $this->feedback($customer, 'farmer', 'review', 'reviewed', true, true);

        $this->assertSame([$featuredCustomerReview->id], WebsiteFeedback::featuredForHomepage()->pluck('id')->all());
    }

    public function test_admin_can_open_feedback_inbox_and_customer_cannot_feature_feedback(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = User::factory()->create(['role' => 'customer']);

        $this->actingAs($admin)->get('/admin/feedback')->assertOk()->assertSee('Website Reviews');
        $this->actingAs($customer)->patch('/admin/feedback/1/feature')->assertForbidden();
    }

    public function test_admin_must_review_consented_feedback_before_featuring_and_can_unfeature_it(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = User::factory()->create(['role' => 'customer']);
        $feedback = $this->feedback($customer, 'customer', 'review', 'new', true, false);

        $this->actingAs($admin)
            ->patch("/admin/feedback/{$feedback->id}/feature")
            ->assertRedirect();
        $this->assertFalse($feedback->fresh()->is_featured);

        $this->put("/admin/feedback/{$feedback->id}", ['status' => 'reviewed'])->assertRedirect();
        $this->patch("/admin/feedback/{$feedback->id}/feature")->assertRedirect();
        $this->assertTrue($feedback->fresh()->is_featured);
        $this->assertSame([$feedback->id], WebsiteFeedback::featuredForHomepage()->pluck('id')->all());

        $this->patch("/admin/feedback/{$feedback->id}/feature")->assertRedirect();
        $this->assertFalse($feedback->fresh()->is_featured);
    }

    private function feedback(User $user, string $submitterRole, string $type, string $status, bool $consent, bool $featured): WebsiteFeedback
    {
        return WebsiteFeedback::create([
            'user_id' => $user->id,
            'submitter_role' => $submitterRole,
            'type' => $type,
            'subject' => 'Website review',
            'message' => 'A useful test review for homepage display.',
            'rating' => 5,
            'status' => $status,
            'public_consent' => $consent,
            'is_featured' => $featured,
        ]);
    }
}
