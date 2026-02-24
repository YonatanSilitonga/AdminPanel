<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Review;
use App\Models\Destination;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewModerationTest extends TestCase
{
    use RefreshDatabase;

    protected Admin $admin;
    protected Review $review;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed roles and permissions
        $this->artisan('db:seed', ['--class' => 'AdminSeeder']);

        // Create moderator admin
        $moderatorRole = Role::where('name', 'moderator')->first();
        $this->admin = Admin::factory()->create([
            'role_id' => $moderatorRole->id,
        ]);

        // Create a pending review
        $destination = Destination::factory()->create();
        $user = User::factory()->create();
        $this->review = Review::factory()->create([
            'destination_id' => $destination->id,
            'user_id' => $user->id,
            'status' => 'pending',
        ]);
    }

    public function test_moderator_can_view_pending_reviews()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.reviews.index', ['status' => 'pending']));

        $response->assertStatus(200);
        $response->assertViewHas('reviews');
    }

    public function test_moderator_can_approve_review()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.reviews.approve', $this->review));

        $this->review->refresh();
        $this->assertEquals('approved', $this->review->status);
        $this->assertEquals($this->admin->id, $this->review->approved_by);
    }

    public function test_moderator_can_reject_review()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.reviews.reject', $this->review), [
                'reason' => 'Contains inappropriate content',
            ]);

        $this->review->refresh();
        $this->assertEquals('rejected', $this->review->status);
    }

    public function test_unauthorized_admin_cannot_moderate_reviews()
    {
        $editorRole = Role::where('name', 'editor')->first();
        $editor = Admin::factory()->create([
            'role_id' => $editorRole->id,
        ]);

        $response = $this->actingAs($editor, 'admin')
            ->post(route('admin.reviews.approve', $this->review));

        $response->assertStatus(403);
    }
}
