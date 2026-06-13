<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\MongoDB\MongoReview;
use App\Models\Role;
use Tests\TestCase;

class ReviewModerationTest extends TestCase
{
    protected Admin $admin;
    protected MongoReview $review;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles and permissions
        $this->artisan('db:seed', ['--class' => 'AdminSeeder']);

        // Create moderator admin
        $moderatorRole = Role::firstOrCreate(['name' => 'moderator'], ['description' => 'Moderator role']);
        $this->admin = Admin::factory()->create([
            'role_id' => $moderatorRole->id,
        ]);

        // Create a pending review directly in the 'ratings' collection (what MongoReview uses)
        $this->review = MongoReview::create([
            'destination_id' => null,
            'user_id'        => null,
            'rating'         => 4,
            'review'         => 'Test review content.',
            'status'         => 'pending',
        ]);
    }

    protected function tearDown(): void
    {
        // Clean up the test review from the ratings collection
        if (isset($this->review)) {
            $this->review->delete();
        }

        parent::tearDown();
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
        $this->withoutExceptionHandling();

        $response = $this->actingAs($this->admin, 'admin')
            ->patch(route('admin.reviews.approve', $this->review->_id));

        $this->review->refresh();
        $this->assertEquals('approved', $this->review->status);
        $this->assertEquals($this->admin->id, $this->review->approved_by);
    }

    public function test_moderator_can_reject_review()
    {
        $this->withoutExceptionHandling();

        $response = $this->actingAs($this->admin, 'admin')
            ->patch(route('admin.reviews.reject', $this->review->_id), [
                'reason' => 'Contains inappropriate content',
            ]);

        $this->review->refresh();
        $this->assertEquals('rejected', $this->review->status);
    }

    public function test_unauthorized_admin_cannot_moderate_reviews()
    {
        $editorRole = Role::firstOrCreate(['name' => 'editor'], ['description' => 'Editor role']);
        $editor = Admin::factory()->create([
            'role_id' => $editorRole->id,
        ]);

        $response = $this->actingAs($editor, 'admin')
            ->patch(route('admin.reviews.approve', $this->review->_id));

        $response->assertStatus(403);
    }
}
