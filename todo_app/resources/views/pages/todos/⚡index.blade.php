<?php

use App\Models\Todo;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Todo 一覧')] class extends Component {
    public string $category = '';

    #[Computed]
    public function todos(): Collection
    {
        return Todo::query()
            ->with('user')
            ->when($this->category !== '', fn ($query) => $query->where('category', $this->category))
            ->orderBy('is_done')
            ->orderBy('due_at')
            ->get();
    }

    #[Computed]
    public function categories(): SupportCollection
    {
        return Todo::query()->distinct()->orderBy('category')->pluck('category');
    }

    public function toggle(int $todoId): void
    {
        $todo = Todo::findOrFail($todoId);

        abort_unless($todo->isOwnedBy(auth()->user()), 403);

        $todo->update(['is_done' => ! $todo->is_done]);

        unset($this->todos);
    }
}; ?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">Todo 一覧</flux:heading>
        @auth
            <flux:button :href="route('todos.create')" variant="primary" icon="plus" wire:navigate>Todo を登録</flux:button>
        @endauth
    </div>

    @if (session('status'))
        <flux:callout variant="success" icon="check-circle">
            <flux:callout.heading>{{ session('status') }}</flux:callout.heading>
        </flux:callout>
    @endif

    <div class="max-w-xs">
        <flux:select wire:model.live="category" label="カテゴリで絞り込む">
            <flux:select.option value="">すべて</flux:select.option>
            @foreach ($this->categories as $categoryOption)
                <flux:select.option :value="$categoryOption">{{ $categoryOption }}</flux:select.option>
            @endforeach
        </flux:select>
    </div>

    @if ($this->todos->isEmpty())
        <flux:text>Todo はまだありません。</flux:text>
    @else
        <flux:table>
            <flux:table.columns>
                <flux:table.column>やること</flux:table.column>
                <flux:table.column>期限</flux:table.column>
                <flux:table.column>カテゴリ</flux:table.column>
                <flux:table.column>作成者</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @foreach ($this->todos as $todo)
                    <flux:table.row wire:key="todo-{{ $todo->id }}">
                        <flux:table.cell variant="strong">
                            <div class="flex items-center gap-3">
                                <flux:checkbox :checked="$todo->is_done"
                                    :disabled="!$todo->isOwnedBy(auth()->user())"
                                    wire:change="toggle({{ $todo->id }})" />
                                <span @class(['line-through text-zinc-400 dark:text-zinc-500' => $todo->is_done])>{{ $todo->title }}</span>
                            </div>
                        </flux:table.cell>
                        <flux:table.cell>{{ $todo->due_at->isoFormat('M月D日(ddd) HH:mm') }}</flux:table.cell>
                        <flux:table.cell>{{ $todo->category }}</flux:table.cell>
                        <flux:table.cell>{{ $todo->user->name }}</flux:table.cell>
                        <flux:table.cell>
                            @if ($todo->isOwnedBy(auth()->user()))
                                <flux:button :href="route('todos.edit', $todo)" size="sm" icon="pencil-square"
                                    wire:navigate>編集</flux:button>
                            @endif
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    @endif
</div>
