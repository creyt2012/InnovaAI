<?php

namespace Tests\Feature\Admin;

use App\Models\PromptTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PromptTemplateTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Tạo admin user
        $this->admin = User::factory()->create([
            'role' => 'admin'
        ]);

        // Tạo normal user
        $this->user = User::factory()->create([
            'role' => 'user'
        ]);
    }

    /** @test */
    public function admin_can_view_prompt_templates_list()
    {
        $template = PromptTemplate::factory()->create([
            'name' => 'Test Template',
            'content' => 'This is a test template'
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.prompts.index'));

        $response->assertStatus(200)
            ->assertSee('Test Template')
            ->assertViewHas('templates');
    }

    /** @test */
    public function normal_user_cannot_view_prompt_templates()
    {
        $response = $this->actingAs($this->user)
            ->get(route('admin.prompts.index'));

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_create_prompt_template()
    {
        $templateData = [
            'name' => 'New Template',
            'content' => 'This is a template with {variable}',
            'category' => 'general',
            'parameters' => [
                ['name' => 'variable', 'required' => true]
            ],
            'is_active' => true
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.prompts.store'), $templateData);

        $response->assertRedirect(route('admin.prompts.index'));
        
        $this->assertDatabaseHas('prompt_templates', [
            'name' => 'New Template',
            'category' => 'general'
        ]);
    }

    /** @test */
    public function admin_can_update_prompt_template()
    {
        $template = PromptTemplate::factory()->create();
        
        $updatedData = [
            'name' => 'Updated Template',
            'content' => 'Updated content with {variable}',
            'category' => 'general',
            'parameters' => [
                ['name' => 'variable', 'required' => true]
            ],
            'is_active' => true
        ];

        $response = $this->actingAs($this->admin)
            ->put(route('admin.prompts.update', $template), $updatedData);

        $response->assertRedirect(route('admin.prompts.index'));
        
        $this->assertDatabaseHas('prompt_templates', [
            'id' => $template->id,
            'name' => 'Updated Template',
            'content' => 'Updated content with {variable}'
        ]);
    }

    /** @test */
    public function admin_can_delete_prompt_template()
    {
        $template = PromptTemplate::factory()->create();

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.prompts.delete', $template));

        $response->assertRedirect(route('admin.prompts.index'));
        
        $this->assertDatabaseMissing('prompt_templates', [
            'id' => $template->id
        ]);
    }

    /** @test */
    public function validate_required_fields_when_creating_template()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.prompts.store'), [
                'name' => '',
                'content' => '',
                'category' => ''
            ]);

        $response->assertSessionHasErrors(['name', 'content', 'category']);
    }

    /** @test */
    public function can_compile_template_with_parameters()
    {
        $template = PromptTemplate::factory()->create([
            'content' => 'Hello {name}, welcome to {platform}!',
            'parameters' => [
                ['name' => 'name', 'required' => true],
                ['name' => 'platform', 'required' => true]
            ]
        ]);

        $compiled = $template->compile([
            'name' => 'John',
            'platform' => 'Laravel'
        ]);

        $this->assertEquals('Hello John, welcome to Laravel!', $compiled);
    }

    /** @test */
    public function validate_parameters_before_compiling()
    {
        $template = PromptTemplate::factory()->create([
            'content' => 'Hello {name}!',
            'parameters' => [
                ['name' => 'name', 'required' => true]
            ]
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Missing required parameters: name');

        app(PromptService::class)->validateParameters($template, []);
    }

    /** @test */
    public function can_filter_templates_by_category()
    {
        PromptTemplate::factory()->create([
            'category' => 'general'
        ]);

        PromptTemplate::factory()->create([
            'category' => 'support'
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.prompts.index', ['category' => 'general']));

        $response->assertViewHas('templates', function($templates) {
            return $templates->count() === 1 && 
                   $templates->first()->category === 'general';
        });
    }

    /** @test */
    public function can_search_templates()
    {
        PromptTemplate::factory()->create([
            'name' => 'Welcome Message',
            'content' => 'Welcome to our platform'
        ]);

        PromptTemplate::factory()->create([
            'name' => 'Goodbye Message',
            'content' => 'Thank you for using our service'
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.prompts.index', ['search' => 'welcome']));

        $response->assertViewHas('templates', function($templates) {
            return $templates->count() === 1 && 
                   str_contains(strtolower($templates->first()->name), 'welcome');
        });
    }
} 