<?php

use App\Models\Todo;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

new #[Title('Todo を編集')] class extends Component {
    public Todo $todo;

    #[Validate('required|string|max:100')]
    public string $title = '';

    #[Validate('required|string|max:2000')]
    public string $memo = '';

    #[Validate('required|string|max:100')]
    public string $category = '';

    #[Validate('boolean')]
    public bool $is_done = false;

    #[Validate('required|date')]
    public string $start_at = '';

    #[Validate('required|date|after:start_at')]
    public string $due_at = '';

    public function mount(Todo $todo): void
    {
        abort_unless($todo->isOwnedBy(auth()->user()), 403);

        $this->todo = $todo;
        $this->title = $todo->title;
        $this->memo = $todo->memo;
        $this->category = $todo->category;
        $this->is_done = $todo->is_done;
        $this->start_at = $todo->start_at->format('Y-m-d\TH:i');
        $this->due_at = $todo->due_at->format('Y-m-d\TH:i');
    }
    public function save(): void
    {
        abort_unless($this->todo->isOwnedBy(auth()->user()), 403);

        $this->todo->update($this->validate());
        session()->flash('status', 'Todo を更新しました。');
        $this->redirectRoute('todos.index', navigate: true);
    }
    public function delete(): void
    {
        abort_unless($this->todo->isOwnedBy(auth()->user()), 403);

        $this->todo->delete();

        session()->flash('status', 'Todo を削除しました。');

        $this->redirectRoute('todos.index', navigate: true);
    }
};
?>

<div class="mx-auto max-w-2xl space-y-6">
    <flux:heading size="xl">Todo を編集</flux:heading>

    <form wire:submit="save" class="space-y-6">
        <flux:input wire:model="title" label="やること" />
        <flux:textarea wire:model="memo" label="メモ" rows="6" />
        <flux:input wire:model="category" label="カテゴリ" />
        <flux:checkbox wire:model="is_done" label="完了" />

        <div class="grid gap-6 sm:grid-cols-2">
            <flux:input wire:model="start_at" label="着手日時" type="datetime-local" />
            <flux:input wire:model="due_at" label="期限" type="datetime-local" />
        </div>

        <div class="flex justify-between">
            <flux:button wire:click="delete" wire:confirm="この Todo を削除しますか？" variant="danger" icon="trash">削除</flux:button>
            <div class="flex gap-3">
                <flux:button :href="route('todos.index')" variant="ghost" wire:navigate>キャンセル</flux:button>
                <flux:button type="submit" variant="primary">更新する</flux:button>
            </div>
        </div>
    </form>
</div>
