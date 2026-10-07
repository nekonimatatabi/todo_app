<?php

use App\Models\Todo;
use App\Models\User;
use Livewire\Livewire;

it('Todo 一覧が表示される', function () {
    $todo = Todo::factory()->create(['title' => '請求書をまとめる']);

    $this->get(route('todos.index'))
        ->assertOk()
        ->assertSee('請求書をまとめる');
});

it('他人の Todo は編集画面を開けない', function () {
    $todo = Todo::factory()->create();
    $other = User::factory()->create();

    $this->actingAs($other)
        ->get(route('todos.edit', $todo))
        ->assertForbidden();
});

it('完了した Todo には取り消し線が付く', function () {
    Todo::factory()->done()->create(['title' => '請求書をまとめる']);

    $this->get(route('todos.index'))
        ->assertOk()
        ->assertSee('line-through', escape: false);
});

it('自分の Todo は完了を切り替えられる', function () {
    $user = User::factory()->create();
    $todo = Todo::factory()->create(['user_id' => $user->id]);

    Livewire::actingAs($user)
        ->test('pages::todos.index')
        ->call('toggle', $todo->id);

    expect($todo->fresh()->is_done)->toBeTrue();
});

it('他人の Todo は完了を切り替えられない', function () {
    $todo = Todo::factory()->create();
    $other = User::factory()->create();

    Livewire::actingAs($other)
        ->test('pages::todos.index')
        ->call('toggle', $todo->id)
        ->assertForbidden();
});

it('カテゴリで Todo を絞り込める', function () {
    Todo::factory()->create(['title' => '請求書をまとめる', 'category' => '仕事']);
    Todo::factory()->create(['title' => '牛乳を買う', 'category' => '買い物']);

    Livewire::test('pages::todos.index')
        ->set('category', '仕事')
        ->assertSee('請求書をまとめる')
        ->assertDontSee('牛乳を買う');
});
