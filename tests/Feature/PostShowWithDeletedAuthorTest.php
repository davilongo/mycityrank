<?php

namespace Tests\Feature;

use App\Models\Ciudad;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostShowWithDeletedAuthorTest extends TestCase
{
    use RefreshDatabase;

    public function test_post_page_does_not_crash_when_the_author_has_been_deleted(): void
    {
        $author = User::factory()->create();
        $ciudad = Ciudad::create(['nombre' => 'Ciudad de prueba', 'pais' => 'País de prueba']);

        $post = Post::create([
            'title' => 'Post de un autor eliminado',
            'slug' => 'post-autor-eliminado',
            'content' => 'x',
            'image' => '/storage/images/fake.jpg',
            'category' => Post::CATEGORIES[0],
            'ciudad_id' => $ciudad->id,
            'user_id' => $author->id,
        ]);

        $author->delete();

        $this->assertNull($post->fresh()->user_id);

        $this->get(route('posts.show', $post))
            ->assertOk()
            ->assertSee(__('posts.anonymous'));
    }
}
