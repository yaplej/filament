<?php

use Filament\Tests\Models\Post;
use Filament\Tests\Tables\Fixtures\PostsTable;
use Filament\Tests\Tables\TestCase;
use function Pest\Livewire\livewire;

uses(TestCase::class);

it('can filter records by boolean column', function () {
    $posts = Post::factory()->count(10)->create();

    livewire(PostsTable::class)
        ->assertCanSeeTableRecords($posts)
        ->filterTable('is_published')
        ->assertCanSeeTableRecords($posts->where('is_published', true))
        ->assertCanNotSeeTableRecords($posts->where('is_published', false));
});

it('can filter records by relationship', function () {
    $posts = Post::factory()->count(10)->create();

    $author = $posts->first()->author;

    livewire(PostsTable::class)
        ->assertCanSeeTableRecords($posts)
        ->filterTable('author', $author)
        ->assertCanSeeTableRecords($posts->where('author_id', $author->getKey()))
        ->assertCanNotSeeTableRecords($posts->where('author_id', '!=', $author->getKey()));
});

it('can persist filters in the user\'s session', function () {
    $posts = Post::factory()->count(10)->create();

    $unpublishedPosts = $posts->where('is_published', false);

    livewire(PostsTable::class)
        ->assertCanSeeTableRecords($posts)
        ->filterTable('is_published')
        ->assertCanNotSeeTableRecords($unpublishedPosts);

    livewire(PostsTable::class)
        ->assertCanNotSeeTableRecords($unpublishedPosts);

    livewire(PostsTable::class)
        ->resetTableFilters()
        ->assertCanSeeTableRecords($unpublishedPosts);

    livewire(PostsTable::class)
        ->assertCanSeeTableRecords($unpublishedPosts);
});

it('can reset filters', function () {
    $posts = Post::factory()->count(10)->create();

    $unpublishedPosts = $posts->where('is_published', false);

    livewire(PostsTable::class)
        ->filterTable('is_published')
        ->assertCanNotSeeTableRecords($unpublishedPosts)
        ->resetTableFilters()
        ->assertCanSeeTableRecords($unpublishedPosts);
});

it('can remove a filter', function () {
    $posts = Post::factory()->count(10)->create();

    $unpublishedPosts = $posts->where('is_published', false);

    livewire(PostsTable::class)
        ->assertCanSeeTableRecords($posts)
        ->filterTable('is_published')
        ->assertCanNotSeeTableRecords($unpublishedPosts)
        ->removeTableFilter('is_published')
        ->assertCanSeeTableRecords($posts);
});

it('can remove all table filters', function () {
    $posts = Post::factory()->count(10)->create();

    $unpublishedPosts = $posts->where('is_published', false);

    livewire(PostsTable::class)
        ->assertCanSeeTableRecords($posts)
        ->filterTable('is_published')
        ->assertCanNotSeeTableRecords($unpublishedPosts)
        ->removeTableFilters()
        ->assertCanSeeTableRecords($posts);
});

it('can use a custom attribute for the `SelectFilter`', function () {
    $posts = Post::factory()->count(10)->create();

    $unpublishedPosts = $posts->where('is_published', false);

    livewire(PostsTable::class)
        ->assertCanSeeTableRecords($posts)
        ->filterTable('select_filter_attribute', false)
        ->assertCanSeeTableRecords($unpublishedPosts)
        ->filterTable('select_filter_attribute', true)
        ->assertCanNotSeeTableRecords($unpublishedPosts);
});
